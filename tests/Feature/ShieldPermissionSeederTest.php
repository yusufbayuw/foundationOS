<?php

namespace Tests\Feature;

use App\Models\Permission;
use Database\Seeders\ShieldPermissionSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class ShieldPermissionSeederTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_it_idempotently_seeds_resource_page_and_widget_permissions(): void
    {
        $this->seed(ShieldPermissionSeeder::class);

        $this->assertDatabaseHas(Permission::class, [
            'name' => 'ViewAny:Role',
            'guard_name' => 'web',
        ]);
        $this->assertDatabaseHas(Permission::class, [
            'name' => 'View:BillingPage',
            'guard_name' => 'web',
        ]);
        $this->assertDatabaseHas(Permission::class, [
            'name' => 'View:ExecutiveStatsOverview',
            'guard_name' => 'web',
        ]);
        $permissionCount = Permission::query()->count();

        $this->seed(ShieldPermissionSeeder::class);

        $this->assertSame($permissionCount, Permission::query()->count());
    }
}
