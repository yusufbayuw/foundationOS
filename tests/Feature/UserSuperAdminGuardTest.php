<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Core\Models\User;
use Tests\TestCase;

class UserSuperAdminGuardTest extends TestCase
{
    use RefreshDatabase;

    public function test_is_super_admin_cannot_be_mass_assigned(): void
    {
        $user = User::factory()->create([
            'is_super_admin' => true,
        ]);

        $this->assertFalse($user->fresh()->isGlobalSuperAdmin());
    }

    public function test_promote_to_global_super_admin_sets_flag(): void
    {
        $user = User::factory()->create();

        $user->promoteToGlobalSuperAdmin();

        $this->assertTrue($user->fresh()->isGlobalSuperAdmin());
    }

    public function test_factory_super_admin_state_works(): void
    {
        $user = User::factory()->superAdmin()->create();

        $this->assertTrue($user->isGlobalSuperAdmin());
    }
}
