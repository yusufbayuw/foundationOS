<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Modules\MerchOrder\Exceptions\DuplicateUniformPackageCodeException;
use Modules\MerchOrder\Models\UniformPackage;
use Modules\MerchOrder\Services\UniformPackageRegistrationService;
use Tests\Concerns\CreatesTenantForTests;
use Tests\TestCase;

class UniformPackageRegistrationServiceTest extends TestCase
{
    use CreatesTenantForTests;
    use LazilyRefreshDatabase;

    public function test_register_creates_active_package_with_normalized_code(): void
    {
        ['tenant' => $tenant, 'organization' => $organization] = $this->makeTenantContext(['core', 'merchorder']);

        $package = app(UniformPackageRegistrationService::class)->register(
            tenantId: $tenant->id,
            organizationId: $organization->id,
            code: ' g7 ',
            name: 'Grade 7 Uniform',
        );

        $this->assertSame('G7', $package->code);
        $this->assertDatabaseHas(UniformPackage::class, ['id' => $package->id, 'code' => 'G7']);
    }

    public function test_register_rejects_duplicate_code_in_same_scope(): void
    {
        ['tenant' => $tenant, 'organization' => $organization] = $this->makeTenantContext(['core', 'merchorder']);

        $service = app(UniformPackageRegistrationService::class);
        $service->register($tenant->id, $organization->id, 'G8', 'Grade 8');

        $this->expectException(DuplicateUniformPackageCodeException::class);
        $service->register($tenant->id, $organization->id, 'g8', 'Duplicate');
    }
}
