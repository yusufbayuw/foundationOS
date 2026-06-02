<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Modules\Clinic\Exceptions\DuplicateAllergyCodeException;
use Modules\Clinic\Models\Allergy;
use Modules\Clinic\Services\AllergyRegistrationService;
use Tests\Concerns\CreatesTenantForTests;
use Tests\TestCase;

class AllergyRegistrationServiceTest extends TestCase
{
    use CreatesTenantForTests;
    use LazilyRefreshDatabase;

    public function test_register_creates_active_allergy_with_normalized_code(): void
    {
        ['tenant' => $tenant, 'organization' => $organization] = $this->makeTenantContext(['core', 'clinic']);

        $allergy = app(AllergyRegistrationService::class)->register(
            tenantId: $tenant->id,
            organizationId: $organization->id,
            code: ' pn ',
            name: 'Peanuts',
        );

        $this->assertSame('PN', $allergy->code);
        $this->assertDatabaseHas(Allergy::class, ['id' => $allergy->id, 'code' => 'PN']);
    }

    public function test_register_rejects_duplicate_code_in_same_scope(): void
    {
        ['tenant' => $tenant, 'organization' => $organization] = $this->makeTenantContext(['core', 'clinic']);

        $service = app(AllergyRegistrationService::class);
        $service->register($tenant->id, $organization->id, 'LAC', 'Lactose');

        $this->expectException(DuplicateAllergyCodeException::class);
        $service->register($tenant->id, $organization->id, 'lac', 'Duplicate');
    }
}
