<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Modules\Counseling\Exceptions\DuplicateCounselorCodeException;
use Modules\Counseling\Models\Counselor;
use Modules\Counseling\Services\CounselorRegistrationService;
use Tests\Concerns\CreatesTenantForTests;
use Tests\TestCase;

class CounselorRegistrationServiceTest extends TestCase
{
    use CreatesTenantForTests;
    use LazilyRefreshDatabase;

    public function test_register_creates_active_counselor_with_normalized_code(): void
    {
        ['tenant' => $tenant, 'organization' => $organization] = $this->makeTenantContext(['core', 'counseling']);

        $counselor = app(CounselorRegistrationService::class)->register(
            tenantId: $tenant->id,
            organizationId: $organization->id,
            code: ' c01 ',
            name: 'Dr. Siti',
        );

        $this->assertSame('C01', $counselor->code);
        $this->assertDatabaseHas(Counselor::class, ['id' => $counselor->id, 'code' => 'C01']);
    }

    public function test_register_rejects_duplicate_code_in_same_scope(): void
    {
        ['tenant' => $tenant, 'organization' => $organization] = $this->makeTenantContext(['core', 'counseling']);

        $service = app(CounselorRegistrationService::class);
        $service->register($tenant->id, $organization->id, 'C02', 'Counselor B');

        $this->expectException(DuplicateCounselorCodeException::class);
        $service->register($tenant->id, $organization->id, 'c02', 'Duplicate');
    }
}
