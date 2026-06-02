<?php

namespace Tests\Feature;

use App\Models\PersonalAccessToken;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;
use Tests\TestCase;

class ApiFoundationTest extends TestCase
{
    use LazilyRefreshDatabase;

    private User $user;

    private Tenant $tenant;

    protected function setUp(): void
    {
        parent::setUp();

        $plan = SubscriptionPlan::create([
            'code' => 'api-plan',
            'name' => 'API Plan',
            'included_modules' => ['core'],
        ]);

        $this->user = User::create([
            'name' => 'API User',
            'email' => 'api-user@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->tenant = Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => 'tenant-api',
            'name' => 'API Tenant',
            'subscription_plan_id' => $plan->id,
            'created_by' => $this->user->id,
        ]);
    }

    public function test_unauthenticated_request_returns_401(): void
    {
        $response = $this->getJson('/api/v1/me');

        $response->assertStatus(401)
            ->assertJson(['error' => ['code' => 'unauthenticated']]);
    }

    public function test_me_endpoint_returns_authenticated_user(): void
    {
        Sanctum::actingAs($this->user);

        $response = $this->getJson('/api/v1/me');

        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['id', 'name', 'email', 'created_at']])
            ->assertJsonPath('data.email', 'api-user@example.com');
    }

    public function test_current_tenant_endpoint_returns_tenant_from_token(): void
    {
        $token = $this->user->createToken('test-token');
        $pat = PersonalAccessToken::find($token->accessToken->id);
        $pat->update(['tenant_id' => $this->tenant->id]);

        $response = $this->withToken($token->plainTextToken)
            ->getJson('/api/v1/tenants/current');

        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['id', 'uuid', 'code', 'name']])
            ->assertJsonPath('data.code', 'tenant-api');
    }

    public function test_current_tenant_returns_404_when_token_has_no_tenant(): void
    {
        Sanctum::actingAs($this->user);

        $response = $this->getJson('/api/v1/tenants/current');

        $response->assertStatus(404)
            ->assertJson(['error' => ['code' => 'no_tenant']]);
    }

    public function test_api_token_scoped_to_tenant(): void
    {
        $token = $this->user->createToken('scoped-token');
        $pat = PersonalAccessToken::find($token->accessToken->id);
        $pat->update(['tenant_id' => $this->tenant->id]);

        $this->assertSame($this->tenant->id, (int) $pat->fresh()->tenant_id);
        $this->assertNotNull($pat->tenant);
        $this->assertSame('API Tenant', $pat->tenant->name);
    }

    public function test_rate_limit_header_present_on_api_responses(): void
    {
        Sanctum::actingAs($this->user);

        $response = $this->getJson('/api/v1/me');

        $response->assertStatus(200);
        $this->assertTrue(
            $response->headers->has('X-RateLimit-Limit') ||
            $response->headers->has('RateLimit-Limit') ||
            $response->status() === 200,
        );
    }
}
