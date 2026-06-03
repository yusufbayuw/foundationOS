<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Modules\InternalAudit\Exceptions\DuplicateAuditProgramCodeException;
use Modules\InternalAudit\Models\AuditProgram;
use Modules\InternalAudit\Services\AuditProgramRegistrationService;
use Tests\Concerns\CreatesTenantForTests;
use Tests\TestCase;

class AuditProgramRegistrationServiceTest extends TestCase
{
    use CreatesTenantForTests;
    use LazilyRefreshDatabase;

    public function test_register_creates_active_program_with_normalized_code(): void
    {
        ['tenant' => $tenant, 'organization' => $organization] = $this->makeTenantContext(['core', 'internalaudit']);

        $program = app(AuditProgramRegistrationService::class)->register(
            tenantId: $tenant->id,
            organizationId: $organization->id,
            code: ' ap1 ',
            name: 'Annual Audit Program',
        );

        $this->assertSame('AP1', $program->code);
        $this->assertDatabaseHas(AuditProgram::class, ['id' => $program->id, 'code' => 'AP1']);
    }

    public function test_register_rejects_duplicate_code_in_same_scope(): void
    {
        ['tenant' => $tenant, 'organization' => $organization] = $this->makeTenantContext(['core', 'internalaudit']);

        $service = app(AuditProgramRegistrationService::class);
        $service->register($tenant->id, $organization->id, 'AP2', 'Program Two');

        $this->expectException(DuplicateAuditProgramCodeException::class);
        $service->register($tenant->id, $organization->id, 'ap2', 'Duplicate');
    }
}
