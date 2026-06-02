<?php

namespace Tests\Feature;

use App\Models\PersonalAccessToken;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Str;
use Modules\Core\Models\Organization;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;
use Modules\Enrollment\Models\AdmissionPeriod;
use Tests\TestCase;

class ApiTenantTokenRequirementTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_api_rejects_token_without_tenant_id(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('no-tenant')->plainTextToken;

        $response = $this->withToken($token)->getJson('/api/v1/organizations');

        $response->assertForbidden();
    }

    public function test_api_accepts_token_with_tenant_id(): void
    {
        [$user, $token, $tenant, $organization] = $this->makeScopedToken();

        AdmissionPeriod::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'name' => 'PPDB',
            'code' => 'PPDB',
            'start_date' => '2026-01-01',
            'end_date' => '2026-03-31',
        ]);

        $response = $this->withToken($token)->getJson('/api/v1/applicants');

        $response->assertOk();
    }

    /**
     * @return array{0:User,1:string,2:Tenant,3:Organization}
     */
    protected function makeScopedToken(): array
    {
        $plan = SubscriptionPlan::create([
            'code' => 'api-tenant-req',
            'name' => 'API Tenant Req',
            'included_modules' => ['core', 'enrollment'],
        ]);

        $user = User::factory()->create();

        $tenant = Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => 'api-tenant',
            'name' => 'API Tenant',
            'subscription_plan_id' => $plan->id,
            'created_by' => $user->id,
        ]);

        $organization = Organization::create([
            'tenant_id' => $tenant->id,
            'name' => 'School',
            'code' => 'SCH',
        ]);

        $created = $user->createToken('scoped');
        PersonalAccessToken::find($created->accessToken->id)?->update(['tenant_id' => $tenant->id]);

        return [$user, $created->plainTextToken, $tenant, $organization];
    }
}
