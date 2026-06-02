<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Modules\Transport\Exceptions\DuplicateRouteCodeException;
use Modules\Transport\Models\Route;
use Modules\Transport\Services\RouteRegistrationService;
use Tests\Concerns\CreatesTenantForTests;
use Tests\TestCase;

class RouteRegistrationServiceTest extends TestCase
{
    use CreatesTenantForTests;
    use LazilyRefreshDatabase;

    public function test_register_creates_route_with_normalized_code(): void
    {
        ['tenant' => $tenant, 'organization' => $organization] = $this->makeTenantContext(['core', 'transport']);

        $route = app(RouteRegistrationService::class)->register(
            $tenant->id,
            $organization->id,
            ' am-pm ',
            'Morning Route',
        );

        $this->assertSame('AM-PM', $route->code);
        $this->assertDatabaseHas(Route::class, ['id' => $route->id, 'status' => 'active']);
    }

    public function test_register_rejects_duplicate_code(): void
    {
        ['tenant' => $tenant, 'organization' => $organization] = $this->makeTenantContext(['core', 'transport']);

        $service = app(RouteRegistrationService::class);
        $service->register($tenant->id, $organization->id, 'R1', 'Route One');

        $this->expectException(DuplicateRouteCodeException::class);
        $service->register($tenant->id, $organization->id, 'r1', 'Route Two');
    }
}
