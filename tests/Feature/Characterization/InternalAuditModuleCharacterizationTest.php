<?php

namespace Tests\Feature\Characterization;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Str;
use Modules\Core\Models\Organization;
use Modules\InternalAudit\Services\AuditProgramRegistrationService;
use Tests\Concerns\CreatesTenantForTests;
use Tests\TestCase;

/**
 * Characterization tests for InternalAudit registration scoping rules.
 */
class InternalAuditModuleCharacterizationTest extends TestCase
{
    use CreatesTenantForTests;
    use LazilyRefreshDatabase;

    public function test_same_code_allowed_in_different_organization_scopes(): void
    {
        ['tenant' => $tenant, 'organization' => $organizationA] = $this->makeTenantContext(['core', 'internalaudit']);

        $organizationB = Organization::create([
            'tenant_id' => $tenant->id,
            'code' => 'org-b-'.Str::lower(Str::random(4)),
            'name' => 'Organization B',
            'is_active' => true,
        ]);

        $service = app(AuditProgramRegistrationService::class);

        $programA = $service->register($tenant->id, $organizationA->id, 'AP-SHARED', 'Program A');
        $programB = $service->register($tenant->id, $organizationB->id, 'AP-SHARED', 'Program B');

        $this->assertSame('AP-SHARED', $programA->code);
        $this->assertSame('AP-SHARED', $programB->code);
        $this->assertNotSame($programA->id, $programB->id);
    }

    public function test_tenant_wide_code_does_not_block_org_scoped_duplicate(): void
    {
        ['tenant' => $tenant, 'organization' => $organization] = $this->makeTenantContext(['core', 'internalaudit']);

        $service = app(AuditProgramRegistrationService::class);

        $tenantWide = $service->register($tenant->id, null, 'AP-TW', 'Tenant Wide Program');
        $orgScoped = $service->register($tenant->id, $organization->id, 'AP-TW', 'Org Scoped Program');

        $this->assertNull($tenantWide->organization_id);
        $this->assertSame($organization->id, $orgScoped->organization_id);
    }
}
