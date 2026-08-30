<?php

namespace Tests\Feature;

use App\Models\PersonalAccessToken;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Str;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;
use Modules\Event\Models\Event;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class AppEventsApiTest extends TestCase
{
    use LazilyRefreshDatabase;

    private Tenant $tenant;

    private User $user;

    private string $token;

    protected function setUp(): void
    {
        parent::setUp();

        $plan = SubscriptionPlan::create([
            'code' => 'event-api-plan',
            'name' => 'Event API Plan',
            'included_modules' => ['core', 'event'],
        ]);

        $this->user = User::create([
            'name' => 'Event API User',
            'email' => 'event-api@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->tenant = Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => 'event-api-tenant',
            'name' => 'Event API Tenant',
            'subscription_plan_id' => $plan->id,
            'created_by' => $this->user->id,
        ]);

        setPermissionsTeamId($this->tenant->id);
        Permission::findOrCreate('ViewAny:Event', 'web');
        Permission::findOrCreate('View:Event', 'web');
        $this->user->givePermissionTo(['ViewAny:Event', 'View:Event']);
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $createdToken = $this->user->createToken('event-api-token');
        $pat = PersonalAccessToken::find($createdToken->accessToken->id);
        $pat->update(['tenant_id' => $this->tenant->id]);
        $this->token = $createdToken->plainTextToken;
    }

    public function test_guest_can_access_public_events_list(): void
    {
        Event::factory()->create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Public Guest Event',
        ]);

        $response = $this->getJson('/api/v1/app/public/events');

        $response->assertOk()
            ->assertJsonPath('data.0.name', 'Public Guest Event');
    }

    public function test_authenticated_user_can_access_public_events_list(): void
    {
        Event::factory()->create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Authenticated Public Event',
        ]);

        $response = $this->withToken($this->token)->getJson('/api/v1/app/events');

        $response->assertOk()
            ->assertJsonPath('data.0.name', 'Authenticated Public Event');
    }

    public function test_events_can_be_filtered_by_active_and_upcoming(): void
    {
        $matchingEvent = Event::factory()->create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Upcoming Active Event',
            'status' => 'active',
            'meta' => $this->eventMeta(now()->addDays(2)->toIso8601String()),
        ]);

        Event::factory()->create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Upcoming Inactive Event',
            'status' => 'inactive',
            'meta' => $this->eventMeta(now()->addDays(3)->toIso8601String()),
        ]);

        Event::factory()->create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Past Active Event',
            'status' => 'active',
            'meta' => $this->eventMeta(now()->subDay()->toIso8601String()),
        ]);

        $response = $this->withToken($this->token)->getJson('/api/v1/app/events?filter[active]=true&filter[upcoming]=true');

        $response->assertOk();
        $ids = collect($response->json('data'))->pluck('id');

        $this->assertTrue($ids->contains($matchingEvent->id));
        $this->assertCount(1, $ids);
    }

    public function test_event_detail_response_uses_standardized_shape(): void
    {
        $event = Event::factory()->create([
            'tenant_id' => $this->tenant->id,
            'code' => 'EVT-DETAIL',
            'name' => 'Detail Event',
            'description' => 'Detail event description.',
            'meta' => [
                'start_at' => '2026-08-01T09:00:00+00:00',
                'end_at' => '2026-08-01T11:00:00+00:00',
                'location' => 'Main Hall',
                'dresscode' => 'Batik',
                'registration_url' => 'https://example.test/register',
                'cover_image' => 'https://example.test/cover.jpg',
            ],
        ]);

        $response = $this->withToken($this->token)->getJson("/api/v1/app/events/{$event->id}");

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'code',
                    'name',
                    'status',
                    'description',
                    'start_at',
                    'end_at',
                    'location',
                    'dresscode',
                    'registration_url',
                    'cover_image',
                    'organization_id',
                    'created_at',
                ],
            ])
            ->assertJsonPath('data.code', 'EVT-DETAIL')
            ->assertJsonPath('data.location', 'Main Hall')
            ->assertJsonPath('data.dresscode', 'Batik')
            ->assertJsonPath('data.registration_url', 'https://example.test/register')
            ->assertJsonPath('data.cover_image', 'https://example.test/cover.jpg');
    }

    /**
     * @return array<string, string|null>
     */
    private function eventMeta(string $startAt): array
    {
        return [
            'start_at' => $startAt,
            'end_at' => null,
            'location' => 'Auditorium',
            'dresscode' => null,
            'registration_url' => null,
            'cover_image' => null,
        ];
    }
}
