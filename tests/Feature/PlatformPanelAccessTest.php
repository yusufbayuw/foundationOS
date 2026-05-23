<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class PlatformPanelAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_platform_panel_returns_forbidden_without_platform_owner_role(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/platform');

        $response->assertForbidden();
    }

    public function test_platform_owner_can_access_platform_dashboard(): void
    {
        $user = User::factory()->create();

        $role = Role::firstOrCreate(
            ['name' => 'platform_owner', 'guard_name' => 'web'],
        );

        setPermissionsTeamId(0);

        $user->roles()->syncWithoutDetaching([
            $role->id => [
                'model_type' => $user->getMorphClass(),
                'tenant_id' => 0,
            ],
        ]);

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $user = $user->fresh();

        setPermissionsTeamId(0);

        $this->assertTrue($user->hasRole('platform_owner'));
        $this->assertDatabaseHas('model_has_roles', [
            'model_id' => $user->id,
            'model_type' => 'user',
            'tenant_id' => 0,
            'role_id' => $role->id,
        ]);

        $response = $this->actingAs($user)->get('/platform');

        $response->assertOk();
    }
}
