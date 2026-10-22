<?php

namespace Tests\Tenant;

use App\Models\PersonalAccessToken;
use App\Models\TenantServiceIntegration;
use App\Support\CurrentTenant;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Modules\Core\Models\Organization;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\TenantRole;
use Modules\Core\Models\User;
use Modules\Core\Models\UserTenantRole;
use Tests\TestCase;

class ApiTenantPrincipalAccessTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'tenancy.api_enforce_membership' => true,
            'tenancy.api_allow_transient_test_token' => false,
        ]);
    }

    protected function tearDown(): void
    {
        app(CurrentTenant::class)->forget();

        parent::tearDown();
    }

    public function test_membership_is_required_in_both_api_versions(): void
    {
        $user = User::factory()->create();
        $tenant = Tenant::factory()->create(['status' => 'active']);
        $token = $this->userToken($user, $tenant);

        foreach (['v1', 'v2'] as $version) {
            $this->withToken($token)->getJson("/api/{$version}/me")
                ->assertForbidden()->assertJsonPath('error.code', 'tenant_membership_required');
        }

        $this->addMembership($user, $tenant);

        foreach (['v1', 'v2'] as $version) {
            $this->withToken($token)->getJson("/api/{$version}/me")->assertOk();
        }
    }

    public function test_removing_membership_revokes_existing_token_immediately(): void
    {
        $user = User::factory()->create();
        $tenant = Tenant::factory()->create(['status' => 'active']);
        $membership = $this->addMembership($user, $tenant);
        $token = $this->userToken($user, $tenant);

        $this->withToken($token)->getJson('/api/v1/me')->assertOk();
        $membership->delete();

        foreach (['v1', 'v2'] as $version) {
            $this->withToken($token)->getJson("/api/{$version}/me")
                ->assertForbidden()->assertJsonPath('error.code', 'tenant_membership_required');
        }
    }

    public function test_expired_membership_is_not_authorized(): void
    {
        $user = User::factory()->create();
        $tenant = Tenant::factory()->create(['status' => 'active']);
        $membership = $this->addMembership($user, $tenant);
        $token = $this->userToken($user, $tenant);

        $membership->update(['expires_at' => now()->subMinute()]);

        $this->withToken($token)->getJson('/api/v2/me')
            ->assertForbidden()->assertJsonPath('error.code', 'tenant_membership_required');
    }

    public function test_membership_of_another_tenant_does_not_authorize_a_token(): void
    {
        $user = User::factory()->create();
        $tenantA = Tenant::factory()->create(['status' => 'active']);
        $tenantB = Tenant::factory()->create(['status' => 'active']);
        $this->addMembership($user, $tenantA);

        $this->withToken($this->userToken($user, $tenantB))
            ->getJson('/api/v1/organizations?tenant_id='.$tenantA->getKey())
            ->assertForbidden()->assertJsonPath('error.code', 'tenant_membership_required');
    }

    public function test_spoofed_tenant_id_does_not_change_token_context(): void
    {
        $user = User::factory()->create();
        $tenantA = Tenant::factory()->create(['status' => 'active']);
        $tenantB = Tenant::factory()->create(['status' => 'active']);
        $this->addMembership($user, $tenantA);
        $token = $this->userToken($user, $tenantA);
        $organizationA = Organization::withoutTenantScope()->create([
            'tenant_id' => $tenantA->id, 'name' => 'Allowed', 'code' => 'ALLOWED',
        ]);
        $organizationB = Organization::withoutTenantScope()->create([
            'tenant_id' => $tenantB->id, 'name' => 'Forbidden', 'code' => 'FORBIDDEN',
        ]);

        foreach (['v1', 'v2'] as $version) {
            $this->withToken($token)
                ->getJson("/api/{$version}/organizations?tenant_id=".$tenantB->id)
                ->assertOk()->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.id', $organizationA->id);

            $this->withToken($token)
                ->getJson("/api/{$version}/organizations/{$organizationB->id}")
                ->assertNotFound();
        }
    }

    public function test_platform_administrator_requires_an_explicit_privileged_ability(): void
    {
        $user = User::factory()->create()->promoteToGlobalSuperAdmin();
        $tenant = Tenant::factory()->create(['status' => 'active']);

        $this->withToken($this->userToken($user, $tenant))
            ->getJson('/api/v1/me')->assertForbidden();

        // Simulate a new real HTTP request: test application reuses its guard instance.
        auth()->forgetGuards();

        $response = $this->withToken($this->userToken($user, $tenant, ['api:read', 'platform:tenants']))
            ->getJson('/api/v1/me');

        $this->assertSame(200, $response->status(), $response->getContent());
    }

    public function test_expired_tenant_subscription_or_trial_is_denied(): void
    {
        $user = User::factory()->create();
        $tenant = Tenant::factory()->create(['status' => 'active', 'subscription_expires_at' => now()->subMinute()]);
        $this->addMembership($user, $tenant);
        $token = $this->userToken($user, $tenant);

        $this->withToken($token)->getJson('/api/v1/me')
            ->assertForbidden()->assertJsonPath('error.code', 'tenant_subscription_inactive');

        $tenant->update(['status' => 'trial', 'subscription_expires_at' => null, 'trial_ends_at' => now()->subMinute()]);

        $this->withToken($token)->getJson('/api/v2/me')
            ->assertForbidden()->assertJsonPath('error.code', 'tenant_subscription_inactive');

        $tenant->update(['status' => 'unknown', 'trial_ends_at' => null]);

        $this->withToken($token)->getJson('/api/v2/me')
            ->assertForbidden()->assertJsonPath('error.code', 'tenant_subscription_inactive');
    }

    public function test_service_principal_is_tenant_bound_read_only_and_not_a_user(): void
    {
        $tenantA = Tenant::factory()->create(['status' => 'active']);
        $tenantB = Tenant::factory()->create(['status' => 'active']);
        $organizationA = Organization::withoutTenantScope()->create([
            'tenant_id' => $tenantA->id, 'name' => 'Visible', 'code' => 'VISIBLE',
        ]);
        $organizationB = Organization::withoutTenantScope()->create([
            'tenant_id' => $tenantB->id, 'name' => 'Hidden', 'code' => 'HIDDEN',
        ]);
        $integration = TenantServiceIntegration::create([
            'tenant_id' => $tenantA->id,
            'name' => 'Directory client',
        ]);
        $issued = $integration->issueReadToken('directory');
        $token = $issued->plainTextToken;

        $this->assertSame(['api:read'], $issued->accessToken->abilities);
        $this->assertNotNull($issued->accessToken->expires_at);
        $this->assertEquals($tenantA->id, $issued->accessToken->tenant_id);

        foreach (['v1', 'v2'] as $version) {
            $this->withToken($token)->getJson("/api/{$version}/organizations?tenant_id=".$tenantB->id)
                ->assertOk()->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.id', $organizationA->id);

            $this->withToken($token)->getJson("/api/{$version}/organizations/{$organizationB->id}")
                ->assertNotFound();

            $this->withToken($token)->getJson("/api/{$version}/me")
                ->assertForbidden()->assertJsonPath('error.code', 'service_integration_denied');

            $this->withToken($token)->getJson("/api/{$version}/students")
                ->assertForbidden()->assertJsonPath('error.code', 'service_integration_denied');
        }
    }

    public function test_service_revocation_and_tenant_mismatch_deny_existing_token(): void
    {
        $tenant = Tenant::factory()->create(['status' => 'active']);
        $otherTenant = Tenant::factory()->create(['status' => 'active']);
        $integration = TenantServiceIntegration::create(['tenant_id' => $tenant->id, 'name' => 'Sync']);
        $issued = $integration->issueReadToken('sync');
        $token = $issued->plainTextToken;

        $this->withToken($token)->getJson('/api/v1/organizations')->assertOk();

        $integration->update(['tenant_id' => $otherTenant->id]);
        $this->withToken($token)->getJson('/api/v1/organizations')
            ->assertForbidden()->assertJsonPath('error.code', 'service_integration_denied');

        $integration->update(['tenant_id' => $tenant->id, 'revoked_at' => now()]);
        $this->withToken($token)->getJson('/api/v2/organizations')
            ->assertForbidden()->assertJsonPath('error.code', 'service_integration_denied');
    }

    public function test_unpersisted_sanctum_test_actor_cannot_bypass_strict_mode(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $this->getJson('/api/v1/me')
            ->assertForbidden()->assertJsonPath('error.code', 'tenant_token_required');
    }

    private function userToken(User $user, Tenant $tenant, array $abilities = ['*']): string
    {
        $issued = $user->createToken('membership-test', $abilities);

        PersonalAccessToken::query()->whereKey($issued->accessToken->getKey())
            ->update(['tenant_id' => $tenant->getKey()]);

        return $issued->plainTextToken;
    }

    private function addMembership(User $user, Tenant $tenant): UserTenantRole
    {
        $role = TenantRole::create([
            'tenant_id' => $tenant->id,
            'name' => 'Member',
            'slug' => 'member-'.$tenant->id,
            'permissions' => [],
        ]);

        return UserTenantRole::create([
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'tenant_role_id' => $role->id,
            'assigned_at' => now(),
        ]);
    }
}
