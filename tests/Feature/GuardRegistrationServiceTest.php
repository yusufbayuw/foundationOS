<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Modules\PhysicalSecurity\Exceptions\DuplicateGuardCodeException;
use Modules\PhysicalSecurity\Models\Guard;
use Modules\PhysicalSecurity\Services\GuardRegistrationService;
use Tests\Concerns\CreatesTenantForTests;
use Tests\TestCase;

class GuardRegistrationServiceTest extends TestCase
{
    use CreatesTenantForTests;
    use LazilyRefreshDatabase;

    public function test_register_creates_active_guard_with_normalized_code(): void
    {
        ['tenant' => $tenant, 'organization' => $organization] = $this->makeTenantContext(['core', 'physicalsecurity']);

        $guard = app(GuardRegistrationService::class)->register(
            tenantId: $tenant->id,
            organizationId: $organization->id,
            code: ' g01 ',
            name: 'Gate A Shift',
        );

        $this->assertSame('G01', $guard->code);
        $this->assertDatabaseHas(Guard::class, ['id' => $guard->id, 'code' => 'G01']);
    }

    public function test_register_rejects_duplicate_code_in_same_scope(): void
    {
        ['tenant' => $tenant, 'organization' => $organization] = $this->makeTenantContext(['core', 'physicalsecurity']);

        $service = app(GuardRegistrationService::class);
        $service->register($tenant->id, $organization->id, 'G02', 'Night shift');

        $this->expectException(DuplicateGuardCodeException::class);
        $service->register($tenant->id, $organization->id, 'g02', 'Duplicate');
    }
}
