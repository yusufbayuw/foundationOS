<?php

namespace Tests\Feature\Characterization;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Str;
use Modules\Clinic\Services\AllergyRegistrationService;
use Modules\Core\Models\Organization;
use Tests\Concerns\CreatesTenantForTests;
use Tests\TestCase;

/**
 * Characterization tests for Clinic allergy registration scoping rules.
 */
class ClinicModuleCharacterizationTest extends TestCase
{
    use CreatesTenantForTests;
    use LazilyRefreshDatabase;

    public function test_same_allergy_code_allowed_in_different_organization_scopes(): void
    {
        ['tenant' => $tenant, 'organization' => $organizationA] = $this->makeTenantContext(['core', 'clinic']);

        $organizationB = Organization::create([
            'tenant_id' => $tenant->id,
            'code' => 'clinic-b-'.Str::lower(Str::random(4)),
            'name' => 'Clinic Org B',
            'is_active' => true,
        ]);

        $service = app(AllergyRegistrationService::class);

        $allergyA = $service->register($tenant->id, $organizationA->id, 'PN', 'Peanuts A');
        $allergyB = $service->register($tenant->id, $organizationB->id, 'PN', 'Peanuts B');

        $this->assertSame('PN', $allergyA->code);
        $this->assertSame('PN', $allergyB->code);
        $this->assertNotSame($allergyA->id, $allergyB->id);
    }

    public function test_tenant_wide_allergy_code_does_not_block_org_scoped_duplicate(): void
    {
        ['tenant' => $tenant, 'organization' => $organization] = $this->makeTenantContext(['core', 'clinic']);

        $service = app(AllergyRegistrationService::class);

        $tenantWide = $service->register($tenant->id, null, 'LAC', 'Tenant Lactose');
        $orgScoped = $service->register($tenant->id, $organization->id, 'LAC', 'Org Lactose');

        $this->assertNull($tenantWide->organization_id);
        $this->assertSame($organization->id, $orgScoped->organization_id);
    }
}
