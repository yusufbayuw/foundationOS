<?php

namespace Tests\Tenant;

use App\Models\PersonalAccessToken;
use App\Support\CurrentTenant;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;
use Tests\TestCase;

class ApiTenantSubscriptionAccessTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function tearDown(): void
    {
        app(CurrentTenant::class)->forget();

        parent::tearDown();
    }

    public function test_suspended_tenant_tokens_cannot_access_v1_or_v2(): void
    {
        $tenant = Tenant::factory()->create(['status' => 'suspended']);
        $token = $this->tokenForTenant($tenant);

        foreach (['v1', 'v2'] as $version) {
            $this->withToken($token)->getJson("/api/{$version}/me")->assertForbidden();
        }
    }

    public function test_past_due_without_grace_period_blocks_tenant_api(): void
    {
        $tenant = Tenant::factory()->create([
            'status' => 'past_due',
            'grace_period_ends_at' => null,
        ]);

        $this->withToken($this->tokenForTenant($tenant))
            ->getJson('/api/v2/me')
            ->assertForbidden();
    }

    public function test_trial_tenant_preserves_api_access(): void
    {
        $tenant = Tenant::factory()->create(['status' => 'trial']);

        $this->withToken($this->tokenForTenant($tenant))
            ->getJson('/api/v1/me')
            ->assertOk();
    }

    public function test_expired_grace_period_blocks_tenant_api(): void
    {
        $tenant = Tenant::factory()->create([
            'status' => 'past_due',
            'grace_period_ends_at' => now()->subMinute(),
        ]);

        $this->withToken($this->tokenForTenant($tenant))
            ->getJson('/api/v1/me')
            ->assertForbidden();
    }

    public function test_grace_period_preserves_tenant_api_access(): void
    {
        $tenant = Tenant::factory()->create([
            'status' => 'past_due',
            'grace_period_ends_at' => now()->addDay(),
        ]);

        $this->withToken($this->tokenForTenant($tenant))
            ->getJson('/api/v1/me')
            ->assertOk();
    }

    public function test_active_tenant_tokens_keep_access(): void
    {
        $tenant = Tenant::factory()->create(['status' => 'active']);

        $this->withToken($this->tokenForTenant($tenant))
            ->getJson('/api/v2/me')
            ->assertOk();
    }

    public function test_tokens_for_deleted_tenants_are_denied(): void
    {
        $tenant = Tenant::factory()->create(['status' => 'active']);
        $token = $this->tokenForTenant($tenant);
        $tenant->delete();

        $this->withToken($token)
            ->getJson('/api/v1/me')
            ->assertForbidden();
    }

    private function tokenForTenant(Tenant $tenant): string
    {
        $token = User::factory()->create()->createToken('subscription-test');

        PersonalAccessToken::query()
            ->whereKey($token->accessToken->getKey())
            ->update(['tenant_id' => $tenant->getKey()]);

        return $token->plainTextToken;
    }
}
