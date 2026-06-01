<?php

namespace Tests\Concerns;

use Illuminate\Support\Str;
use Modules\Core\Models\Organization;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;

trait InteractsWithPdfDocuments
{
    /**
     * @return array{0: Tenant, 1: Organization, 2: User}
     */
    protected function makePdfTenantContext(): array
    {
        $plan = SubscriptionPlan::create([
            'code' => 'pdf-suite',
            'name' => 'PDF Suite',
            'included_modules' => ['core', 'finance', 'school', 'campus'],
        ]);

        $tenant = Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => 'pdf-tenant',
            'name' => 'PDF Tenant',
            'subscription_plan_id' => $plan->id,
        ]);

        $organization = Organization::create([
            'tenant_id' => $tenant->id,
            'code' => 'main',
            'name' => 'Main Organization',
            'is_main' => true,
        ]);

        $user = User::factory()->create([
            'is_super_admin' => true,
        ]);

        return [$tenant, $organization, $user];
    }
}
