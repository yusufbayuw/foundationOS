<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Modules\Risk\Exceptions\DuplicateRiskCategoryCodeException;
use Modules\Risk\Models\RiskCategory;
use Modules\Risk\Services\RiskCategoryService;
use Tests\Concerns\CreatesTenantForTests;
use Tests\TestCase;

class RiskCategoryServiceTest extends TestCase
{
    use CreatesTenantForTests;
    use LazilyRefreshDatabase;

    public function test_register_creates_active_category_with_normalized_code(): void
    {
        ['tenant' => $tenant, 'organization' => $organization] = $this->makeTenantContext(['core', 'risk']);

        $category = app(RiskCategoryService::class)->register(
            tenantId: $tenant->id,
            organizationId: $organization->id,
            code: ' ops ',
            name: 'Operational Risk',
            description: 'Day-to-day operations',
            meta: ['tier' => 'high'],
        );

        $this->assertSame('OPS', $category->code);
        $this->assertSame('active', $category->status);
        $this->assertSame('high', $category->meta['tier']);
        $this->assertDatabaseHas(RiskCategory::class, [
            'id' => $category->id,
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'code' => 'OPS',
        ]);
    }

    public function test_register_rejects_duplicate_code_in_same_scope(): void
    {
        ['tenant' => $tenant, 'organization' => $organization] = $this->makeTenantContext(['core', 'risk']);

        $service = app(RiskCategoryService::class);

        $service->register($tenant->id, $organization->id, 'FIN', 'Financial');

        $this->expectException(DuplicateRiskCategoryCodeException::class);

        $service->register($tenant->id, $organization->id, 'fin', 'Financial duplicate');
    }

    public function test_deactivate_and_reactivate_toggle_status(): void
    {
        ['tenant' => $tenant, 'organization' => $organization] = $this->makeTenantContext(['core', 'risk']);

        $category = app(RiskCategoryService::class)->register(
            $tenant->id,
            $organization->id,
            'STR',
            'Strategic',
        );

        app(RiskCategoryService::class)->deactivate($category);
        $this->assertSame('inactive', $category->fresh()->status);

        app(RiskCategoryService::class)->reactivate($category->fresh());
        $this->assertSame('active', $category->fresh()->status);
    }
}
