<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Modules\Asset\Exceptions\DuplicateAssetCategoryCodeException;
use Modules\Asset\Models\AssetCategory;
use Modules\Asset\Services\AssetCategoryRegistrationService;
use Tests\Concerns\CreatesTenantForTests;
use Tests\TestCase;

class AssetCategoryRegistrationServiceTest extends TestCase
{
    use CreatesTenantForTests;
    use LazilyRefreshDatabase;

    public function test_register_creates_active_category_with_normalized_code(): void
    {
        ['tenant' => $tenant, 'organization' => $organization] = $this->makeTenantContext(['core', 'asset']);

        $category = app(AssetCategoryRegistrationService::class)->register(
            tenantId: $tenant->id,
            organizationId: $organization->id,
            code: ' it ',
            name: 'IT Equipment',
            description: 'Laptops and peripherals',
            meta: ['depreciation_years' => 3],
        );

        $this->assertSame('IT', $category->code);
        $this->assertSame('active', $category->status);
        $this->assertSame(3, $category->meta['depreciation_years']);
        $this->assertDatabaseHas(AssetCategory::class, [
            'id' => $category->id,
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'code' => 'IT',
        ]);
    }

    public function test_register_rejects_duplicate_code_in_same_scope(): void
    {
        ['tenant' => $tenant, 'organization' => $organization] = $this->makeTenantContext(['core', 'asset']);

        $service = app(AssetCategoryRegistrationService::class);

        $service->register($tenant->id, $organization->id, 'VEH', 'Vehicles');

        $this->expectException(DuplicateAssetCategoryCodeException::class);

        $service->register($tenant->id, $organization->id, 'veh', 'Vehicles duplicate');
    }
}
