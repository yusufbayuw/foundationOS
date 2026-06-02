<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Modules\ItOps\Exceptions\DuplicateSoftwareLicenseCodeException;
use Modules\ItOps\Models\SoftwareLicense;
use Modules\ItOps\Services\SoftwareLicenseRegistrationService;
use Tests\Concerns\CreatesTenantForTests;
use Tests\TestCase;

class SoftwareLicenseRegistrationServiceTest extends TestCase
{
    use CreatesTenantForTests;
    use LazilyRefreshDatabase;

    public function test_register_creates_active_license_with_normalized_code(): void
    {
        ['tenant' => $tenant, 'organization' => $organization] = $this->makeTenantContext(['core', 'itops']);

        $license = app(SoftwareLicenseRegistrationService::class)->register(
            tenantId: $tenant->id,
            organizationId: $organization->id,
            code: ' m365 ',
            name: 'Microsoft 365',
            description: 'Enterprise subscription',
        );

        $this->assertSame('M365', $license->code);
        $this->assertSame('active', $license->status);
        $this->assertDatabaseHas(SoftwareLicense::class, [
            'id' => $license->id,
            'tenant_id' => $tenant->id,
            'code' => 'M365',
        ]);
    }

    public function test_register_rejects_duplicate_code_in_same_scope(): void
    {
        ['tenant' => $tenant, 'organization' => $organization] = $this->makeTenantContext(['core', 'itops']);

        $service = app(SoftwareLicenseRegistrationService::class);

        $service->register($tenant->id, $organization->id, 'ADOBE', 'Adobe CC');

        $this->expectException(DuplicateSoftwareLicenseCodeException::class);

        $service->register($tenant->id, $organization->id, 'adobe', 'Adobe duplicate');
    }
}
