<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Modules\Capacity\Exceptions\DuplicateCapacityResourceCodeException;
use Modules\Capacity\Models\CapacityResource;
use Modules\Capacity\Services\CapacityResourceRegistrationService;
use Tests\Concerns\CreatesTenantForTests;
use Tests\TestCase;

class CapacityResourceRegistrationServiceTest extends TestCase
{
    use CreatesTenantForTests;
    use LazilyRefreshDatabase;

    public function test_register_creates_active_resource_with_normalized_code(): void
    {
        ['tenant' => $tenant, 'organization' => $organization] = $this->makeTenantContext(['core', 'capacity']);

        $resource = app(CapacityResourceRegistrationService::class)->register(
            tenantId: $tenant->id,
            organizationId: $organization->id,
            code: ' lab1 ',
            name: 'Science Lab',
        );

        $this->assertSame('LAB1', $resource->code);
        $this->assertDatabaseHas(CapacityResource::class, ['id' => $resource->id, 'code' => 'LAB1']);
    }

    public function test_register_rejects_duplicate_code_in_same_scope(): void
    {
        ['tenant' => $tenant, 'organization' => $organization] = $this->makeTenantContext(['core', 'capacity']);

        $service = app(CapacityResourceRegistrationService::class);
        $service->register($tenant->id, $organization->id, 'LAB2', 'Computer Lab');

        $this->expectException(DuplicateCapacityResourceCodeException::class);
        $service->register($tenant->id, $organization->id, 'lab2', 'Duplicate');
    }
}
