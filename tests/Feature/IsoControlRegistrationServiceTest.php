<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Modules\IsoCompliance\Exceptions\DuplicateIsoControlCodeException;
use Modules\IsoCompliance\Models\IsoControl;
use Modules\IsoCompliance\Services\IsoControlRegistrationService;
use Tests\Concerns\CreatesTenantForTests;
use Tests\TestCase;

class IsoControlRegistrationServiceTest extends TestCase
{
    use CreatesTenantForTests;
    use LazilyRefreshDatabase;

    public function test_register_creates_active_control_with_normalized_code(): void
    {
        ['tenant' => $tenant, 'organization' => $organization] = $this->makeTenantContext(['core', 'isocompliance']);

        $control = app(IsoControlRegistrationService::class)->register(
            tenantId: $tenant->id,
            organizationId: $organization->id,
            code: ' a01 ',
            name: 'Access Control',
        );

        $this->assertSame('A01', $control->code);
        $this->assertDatabaseHas(IsoControl::class, ['id' => $control->id, 'code' => 'A01']);
    }

    public function test_register_rejects_duplicate_code_in_same_scope(): void
    {
        ['tenant' => $tenant, 'organization' => $organization] = $this->makeTenantContext(['core', 'isocompliance']);

        $service = app(IsoControlRegistrationService::class);
        $service->register($tenant->id, $organization->id, 'A02', 'Control Two');

        $this->expectException(DuplicateIsoControlCodeException::class);
        $service->register($tenant->id, $organization->id, 'a02', 'Duplicate');
    }
}
