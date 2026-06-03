<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Modules\EducationQa\Exceptions\DuplicateQualityStandardCodeException;
use Modules\EducationQa\Models\QualityStandard;
use Modules\EducationQa\Services\QualityStandardRegistrationService;
use Tests\Concerns\CreatesTenantForTests;
use Tests\TestCase;

class QualityStandardRegistrationServiceTest extends TestCase
{
    use CreatesTenantForTests;
    use LazilyRefreshDatabase;

    public function test_register_creates_active_standard_with_normalized_code(): void
    {
        ['tenant' => $tenant, 'organization' => $organization] = $this->makeTenantContext(['core', 'educationqa']);

        $standard = app(QualityStandardRegistrationService::class)->register(
            tenantId: $tenant->id,
            organizationId: $organization->id,
            code: ' qs1 ',
            name: 'Curriculum Quality',
        );

        $this->assertSame('QS1', $standard->code);
        $this->assertDatabaseHas(QualityStandard::class, ['id' => $standard->id, 'code' => 'QS1']);
    }

    public function test_register_rejects_duplicate_code_in_same_scope(): void
    {
        ['tenant' => $tenant, 'organization' => $organization] = $this->makeTenantContext(['core', 'educationqa']);

        $service = app(QualityStandardRegistrationService::class);
        $service->register($tenant->id, $organization->id, 'QS2', 'Standard Two');

        $this->expectException(DuplicateQualityStandardCodeException::class);
        $service->register($tenant->id, $organization->id, 'qs2', 'Duplicate');
    }
}
