<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Modules\Boarding\Exceptions\DuplicateDormitoryCodeException;
use Modules\Boarding\Models\Dormitory;
use Modules\Boarding\Services\DormitoryRegistrationService;
use Tests\Concerns\CreatesTenantForTests;
use Tests\TestCase;

class DormitoryRegistrationServiceTest extends TestCase
{
    use CreatesTenantForTests;
    use LazilyRefreshDatabase;

    public function test_register_creates_active_dormitory_with_normalized_code(): void
    {
        ['tenant' => $tenant, 'organization' => $organization] = $this->makeTenantContext(['core', 'boarding']);

        $dormitory = app(DormitoryRegistrationService::class)->register(
            tenantId: $tenant->id,
            organizationId: $organization->id,
            code: ' blk-a ',
            name: 'Block A',
            description: 'Boys dormitory',
        );

        $this->assertSame('BLK-A', $dormitory->code);
        $this->assertSame('active', $dormitory->status);
        $this->assertDatabaseHas(Dormitory::class, [
            'id' => $dormitory->id,
            'tenant_id' => $tenant->id,
            'code' => 'BLK-A',
        ]);
    }

    public function test_register_rejects_duplicate_code_in_same_scope(): void
    {
        ['tenant' => $tenant, 'organization' => $organization] = $this->makeTenantContext(['core', 'boarding']);

        $service = app(DormitoryRegistrationService::class);

        $service->register($tenant->id, $organization->id, 'BLK-B', 'Block B');

        $this->expectException(DuplicateDormitoryCodeException::class);

        $service->register($tenant->id, $organization->id, 'blk-b', 'Block B duplicate');
    }
}
