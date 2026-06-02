<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Modules\Cafeteria\Exceptions\DuplicateMenuCodeException;
use Modules\Cafeteria\Models\Menu;
use Modules\Cafeteria\Services\MenuRegistrationService;
use Tests\Concerns\CreatesTenantForTests;
use Tests\TestCase;

class MenuRegistrationServiceTest extends TestCase
{
    use CreatesTenantForTests;
    use LazilyRefreshDatabase;

    public function test_register_creates_active_menu_with_normalized_code(): void
    {
        ['tenant' => $tenant, 'organization' => $organization] = $this->makeTenantContext(['core', 'cafeteria']);

        $menu = app(MenuRegistrationService::class)->register(
            tenantId: $tenant->id,
            organizationId: $organization->id,
            code: ' lunch ',
            name: 'Lunch Set A',
            description: 'Daily lunch package',
        );

        $this->assertSame('LUNCH', $menu->code);
        $this->assertSame('active', $menu->status);
        $this->assertDatabaseHas(Menu::class, [
            'id' => $menu->id,
            'tenant_id' => $tenant->id,
            'code' => 'LUNCH',
        ]);
    }

    public function test_register_rejects_duplicate_code_in_same_scope(): void
    {
        ['tenant' => $tenant, 'organization' => $organization] = $this->makeTenantContext(['core', 'cafeteria']);

        $service = app(MenuRegistrationService::class);

        $service->register($tenant->id, $organization->id, 'BRKFST', 'Breakfast');

        $this->expectException(DuplicateMenuCodeException::class);

        $service->register($tenant->id, $organization->id, 'brkfst', 'Breakfast duplicate');
    }
}
