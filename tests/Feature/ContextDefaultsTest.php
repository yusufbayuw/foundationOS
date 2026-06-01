<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Modules\Core\Models\AcademicPeriod;
use Modules\Core\Models\AcademicYear;
use Modules\Core\Models\Organization;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\TenantRole;
use Modules\Core\Models\User;
use Modules\Core\Models\UserTenantRole;
use Modules\Core\Services\ContextDefaults;
use Tests\TestCase;

class ContextDefaultsTest extends TestCase
{
    use RefreshDatabase;

    public function test_resolves_primary_organization_and_active_academic_context(): void
    {
        [$tenant, $organization, $user] = $this->makeContext();

        $year = AcademicYear::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'name' => '2026/2027',
            'code' => '2026-2027',
            'start_date' => '2026-07-01',
            'end_date' => '2027-06-30',
            'is_active' => true,
        ]);

        $period = AcademicPeriod::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'academic_year_id' => $year->id,
            'name' => 'Semester Ganjil',
            'code' => 'GANJIL',
            'start_date' => '2026-07-01',
            'end_date' => '2026-12-31',
            'is_active' => true,
        ]);

        $defaults = app(ContextDefaults::class);

        $this->assertSame((int) $organization->id, $defaults->resolveOrganizationId($user, $tenant->id));
        $this->assertSame((int) $period->id, $defaults->resolveAcademicPeriodId($tenant->id));
        $this->assertSame((int) $year->id, $defaults->resolveAcademicYearId($tenant->id));
    }

    /**
     * @return array{0:Tenant,1:Organization,2:User}
     */
    protected function makeContext(): array
    {
        $plan = SubscriptionPlan::firstOrCreate(
            ['code' => 'context-defaults-test'],
            ['name' => 'Context Defaults Test', 'included_modules' => ['core']],
        );

        $rand = Str::random(4);

        $tenant = Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => "ctx-{$rand}",
            'name' => "Context Tenant {$rand}",
            'subscription_plan_id' => $plan->id,
        ]);

        $organization = Organization::create([
            'tenant_id' => $tenant->id,
            'name' => 'Main Campus',
            'code' => "ORG-{$rand}",
        ]);

        $role = TenantRole::create([
            'tenant_id' => $tenant->id,
            'name' => 'Default',
            'slug' => "default-{$rand}",
            'permissions' => ['*'],
            'is_default' => true,
        ]);

        $user = User::create([
            'name' => 'Staff '.$rand,
            'email' => "staff-{$rand}@example.com",
            'password' => 'password',
        ]);

        UserTenantRole::create([
            'user_id' => $user->id,
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'tenant_role_id' => $role->id,
            'assigned_by' => $user->id,
            'is_primary' => true,
        ]);

        return [$tenant, $organization, $user];
    }
}
