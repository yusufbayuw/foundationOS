<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Modules\Property\Exceptions\DuplicatePropertyCodeException;
use Modules\Property\Models\Property;
use Modules\Property\Services\PropertyRegistrationService;
use Tests\Concerns\CreatesTenantForTests;
use Tests\TestCase;

class PropertyRegistrationServiceTest extends TestCase
{
    use CreatesTenantForTests;
    use LazilyRefreshDatabase;

    public function test_register_creates_property(): void
    {
        ['tenant' => $tenant, 'organization' => $organization] = $this->makeTenantContext(['core', 'property']);

        $property = app(PropertyRegistrationService::class)->register(
            $tenant->id,
            $organization->id,
            'blk-a',
            'Block A Building',
        );

        $this->assertSame('BLK-A', $property->code);
        $this->assertDatabaseHas(Property::class, ['id' => $property->id]);
    }

    public function test_register_rejects_duplicate_code(): void
    {
        ['tenant' => $tenant, 'organization' => $organization] = $this->makeTenantContext(['core', 'property']);

        $service = app(PropertyRegistrationService::class);
        $service->register($tenant->id, $organization->id, 'X1', 'Property X');

        $this->expectException(DuplicatePropertyCodeException::class);
        $service->register($tenant->id, $organization->id, 'x1', 'Property Y');
    }
}
