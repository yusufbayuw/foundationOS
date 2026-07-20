<?php

namespace Tests\Feature;

use App\Models\PersonalAccessToken;
use App\Support\CurrentTenant;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Str;
use Modules\Cms\Models\Banner;
use Modules\Cms\Models\HomepageSection;
use Modules\Cms\Models\Site;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;
use Modules\Donation\Models\Campaign;
use Modules\Event\Models\Event;
use Tests\TestCase;

class AppHomeApiTest extends TestCase
{
    use LazilyRefreshDatabase;

    private User $user;

    private Tenant $tenant;

    private string $token;

    protected function setUp(): void
    {
        parent::setUp();

        $plan = SubscriptionPlan::query()->create([
            'code' => 'app-home-plan',
            'name' => 'App Home Plan',
            'included_modules' => ['cms', 'donation', 'event'],
        ]);

        $this->user = User::query()->create([
            'name' => 'App Home User',
            'email' => 'app-home@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->tenant = Tenant::query()->create([
            'uuid' => (string) Str::uuid(),
            'code' => 'app-home-tenant',
            'name' => 'App Home Tenant',
            'subscription_plan_id' => $plan->id,
            'created_by' => $this->user->id,
        ]);

        app(CurrentTenant::class)->set($this->tenant);

        $createdToken = $this->user->createToken('app-home-token');
        PersonalAccessToken::query()
            ->findOrFail($createdToken->accessToken->id)
            ->update(['tenant_id' => $this->tenant->id]);
        $this->token = $createdToken->plainTextToken;
    }

    protected function tearDown(): void
    {
        app(CurrentTenant::class)->forget();

        parent::tearDown();
    }

    public function test_home_sections_are_returned_in_configured_order(): void
    {
        HomepageSection::query()->create([
            'tenant_id' => $this->tenant->id,
            'type' => HomepageSection::TYPE_FEATURED_EVENTS,
            'title' => 'Events',
            'sort_order' => 20,
            'is_active' => true,
        ]);
        HomepageSection::query()->create([
            'tenant_id' => $this->tenant->id,
            'type' => HomepageSection::TYPE_PROMO_BANNERS,
            'title' => 'Banners',
            'sort_order' => 10,
            'is_active' => true,
        ]);

        $response = $this->withToken($this->token)->getJson('/api/v1/app/home');

        $response->assertOk();
        $this->assertSame([
            HomepageSection::TYPE_PROMO_BANNERS,
            HomepageSection::TYPE_FEATURED_EVENTS,
        ], collect($response->json('data.sections'))->pluck('type')->all());
    }

    public function test_inactive_sections_and_content_are_hidden(): void
    {
        $site = Site::factory()->create(['tenant_id' => $this->tenant->id]);

        HomepageSection::query()->create([
            'tenant_id' => $this->tenant->id,
            'type' => HomepageSection::TYPE_PROMO_BANNERS,
            'title' => 'Banners',
            'sort_order' => 10,
            'is_active' => true,
        ]);
        HomepageSection::query()->create([
            'tenant_id' => $this->tenant->id,
            'type' => HomepageSection::TYPE_FEATURED_EVENTS,
            'title' => 'Events',
            'sort_order' => 15,
            'is_active' => true,
        ]);
        HomepageSection::query()->create([
            'tenant_id' => $this->tenant->id,
            'type' => HomepageSection::TYPE_FEATURED_DONATIONS,
            'title' => 'Hidden Donations',
            'sort_order' => 20,
            'is_active' => false,
        ]);

        Banner::query()->create([
            'tenant_id' => $this->tenant->id,
            'site_id' => $site->id,
            'title_id' => 'Active Banner',
            'sort_order' => 10,
            'is_active' => true,
        ]);
        Banner::query()->create([
            'tenant_id' => $this->tenant->id,
            'site_id' => $site->id,
            'title_id' => 'Inactive Banner',
            'sort_order' => 20,
            'is_active' => false,
        ]);
        Campaign::query()->create([
            'tenant_id' => $this->tenant->id,
            'code' => 'hidden-campaign',
            'name' => 'Hidden Campaign',
            'status' => 'active',
            'is_public' => true,
        ]);
        Event::query()->create([
            'tenant_id' => $this->tenant->id,
            'code' => 'upcoming-event',
            'name' => 'Upcoming Event',
            'status' => 'active',
            'meta' => ['starts_at' => now()->addDay()->toISOString()],
        ]);
        Event::query()->create([
            'tenant_id' => $this->tenant->id,
            'code' => 'past-event',
            'name' => 'Past Event',
            'status' => 'active',
            'meta' => ['starts_at' => now()->subDay()->toISOString()],
        ]);

        $response = $this->withToken($this->token)->getJson('/api/v1/app/home');

        $response->assertOk()
            ->assertJsonMissing(['title' => 'Hidden Donations'])
            ->assertJsonMissing(['title' => 'Inactive Banner'])
            ->assertJsonMissing(['name' => 'Past Event'])
            ->assertJsonPath('data.sections.0.items.0.title', 'Active Banner')
            ->assertJsonPath('data.sections.1.items.0.name', 'Upcoming Event');
    }

    public function test_home_response_is_isolated_to_token_tenant(): void
    {
        $foreignTenant = Tenant::query()->create([
            'uuid' => (string) Str::uuid(),
            'code' => 'foreign-home-tenant',
            'name' => 'Foreign Home Tenant',
            'subscription_plan_id' => $this->tenant->subscription_plan_id,
            'created_by' => $this->user->id,
        ]);

        HomepageSection::query()->create([
            'tenant_id' => $this->tenant->id,
            'type' => HomepageSection::TYPE_FEATURED_DONATIONS,
            'title' => 'Tenant Donations',
            'sort_order' => 10,
            'is_active' => true,
        ]);
        Campaign::query()->create([
            'tenant_id' => $this->tenant->id,
            'code' => 'tenant-campaign',
            'name' => 'Tenant Campaign',
            'status' => 'active',
            'is_public' => true,
        ]);

        app(CurrentTenant::class)->set($foreignTenant);
        HomepageSection::query()->create([
            'tenant_id' => $foreignTenant->id,
            'type' => HomepageSection::TYPE_FEATURED_DONATIONS,
            'title' => 'Foreign Donations',
            'sort_order' => 5,
            'is_active' => true,
        ]);
        Campaign::query()->create([
            'tenant_id' => $foreignTenant->id,
            'code' => 'foreign-campaign',
            'name' => 'Foreign Campaign',
            'status' => 'active',
            'is_public' => true,
        ]);
        Event::query()->create([
            'tenant_id' => $foreignTenant->id,
            'code' => 'foreign-event',
            'name' => 'Foreign Event',
            'status' => 'active',
            'meta' => ['starts_at' => now()->addDay()->toISOString()],
        ]);
        app(CurrentTenant::class)->set($this->tenant);

        $response = $this->withToken($this->token)->getJson('/api/v1/app/home');

        $response->assertOk()
            ->assertJsonPath('data.tenant_id', $this->tenant->id)
            ->assertJsonPath('data.sections.0.title', 'Tenant Donations')
            ->assertJsonPath('data.sections.0.items.0.name', 'Tenant Campaign')
            ->assertJsonMissing(['title' => 'Foreign Donations'])
            ->assertJsonMissing(['name' => 'Foreign Campaign'])
            ->assertJsonMissing(['name' => 'Foreign Event']);
    }
}
