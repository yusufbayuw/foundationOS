<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Modules\Alumni\Exceptions\DuplicateCompanyPartnerCodeException;
use Modules\Alumni\Models\CompanyPartner;
use Modules\Alumni\Services\CompanyPartnerRegistrationService;
use Tests\Concerns\CreatesTenantForTests;
use Tests\TestCase;

class CompanyPartnerRegistrationServiceTest extends TestCase
{
    use CreatesTenantForTests;
    use LazilyRefreshDatabase;

    public function test_register_creates_active_partner_with_normalized_code(): void
    {
        ['tenant' => $tenant, 'organization' => $organization] = $this->makeTenantContext(['core', 'alumni']);

        $partner = app(CompanyPartnerRegistrationService::class)->register(
            tenantId: $tenant->id,
            organizationId: $organization->id,
            code: ' acme ',
            name: 'ACME Corp',
        );

        $this->assertSame('ACME', $partner->code);
        $this->assertDatabaseHas(CompanyPartner::class, ['id' => $partner->id, 'code' => 'ACME']);
    }

    public function test_register_rejects_duplicate_code_in_same_scope(): void
    {
        ['tenant' => $tenant, 'organization' => $organization] = $this->makeTenantContext(['core', 'alumni']);

        $service = app(CompanyPartnerRegistrationService::class);
        $service->register($tenant->id, $organization->id, 'BETA', 'Beta Inc');

        $this->expectException(DuplicateCompanyPartnerCodeException::class);
        $service->register($tenant->id, $organization->id, 'beta', 'Duplicate');
    }
}
