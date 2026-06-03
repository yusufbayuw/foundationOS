<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Modules\KpiEnterprise\Exceptions\DuplicateKpiAreaCodeException;
use Modules\KpiEnterprise\Models\KpiArea;
use Modules\KpiEnterprise\Services\KpiAreaRegistrationService;
use Tests\Concerns\CreatesTenantForTests;
use Tests\TestCase;

class KpiAreaRegistrationServiceTest extends TestCase
{
    use CreatesTenantForTests;
    use LazilyRefreshDatabase;

    public function test_register_creates_active_area_with_normalized_code(): void
    {
        ['tenant' => $tenant, 'organization' => $organization] = $this->makeTenantContext(['core', 'kpienterprise']);

        $area = app(KpiAreaRegistrationService::class)->register(
            tenantId: $tenant->id,
            organizationId: $organization->id,
            code: ' fin ',
            name: 'Financial KPIs',
        );

        $this->assertSame('FIN', $area->code);
        $this->assertDatabaseHas(KpiArea::class, ['id' => $area->id, 'code' => 'FIN']);
    }

    public function test_register_rejects_duplicate_code_in_same_scope(): void
    {
        ['tenant' => $tenant, 'organization' => $organization] = $this->makeTenantContext(['core', 'kpienterprise']);

        $service = app(KpiAreaRegistrationService::class);
        $service->register($tenant->id, $organization->id, 'OPS', 'Operations');

        $this->expectException(DuplicateKpiAreaCodeException::class);
        $service->register($tenant->id, $organization->id, 'ops', 'Duplicate');
    }
}
