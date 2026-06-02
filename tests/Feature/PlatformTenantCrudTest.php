<?php

namespace Tests\Feature;

use App\Filament\Platform\Resources\Tenants\TenantResource;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Str;
use Modules\Core\Models\Tenant;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class PlatformTenantCrudTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_platform_owner_can_access_tenant_crud_pages(): void
    {
        $user = $this->createPlatformOwner();
        $tenant = Tenant::query()->create([
            'uuid' => (string) Str::uuid(),
            'code' => 'crud-tenant',
            'name' => 'CRUD Tenant',
            'status' => 'trial',
            'timezone' => 'Asia/Jakarta',
            'currency' => 'IDR',
            'locale' => 'id',
            'created_by' => $user->getKey(),
        ]);

        $this->actingAs($user);

        $this->assertTrue(TenantResource::canAccess());
        $this->assertTrue(TenantResource::canViewAny());
        $this->assertTrue(TenantResource::canView($tenant));
        $this->assertTrue(TenantResource::canCreate());
        $this->assertTrue(TenantResource::canEdit($tenant));
        $this->assertTrue(TenantResource::canDelete($tenant));

        $this->get('/platform')->assertOk();
        $this->get('/platform/tenants')->assertOk();
        $this->get('/platform/tenants/'.$tenant->uuid)->assertOk();
        $this->get('/platform/tenants/create')->assertOk();
        $this->get('/platform/tenants/'.$tenant->uuid.'/edit')->assertOk();
    }

    private function createPlatformOwner(): User
    {
        $user = User::factory()->create();
        $role = Role::firstOrCreate(['name' => 'platform_owner', 'guard_name' => 'web']);

        setPermissionsTeamId(0);

        $user->roles()->syncWithoutDetaching([
            $role->id => [
                'model_type' => $user->getMorphClass(),
                'tenant_id' => 0,
            ],
        ]);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
        setPermissionsTeamId(0);

        return $user->fresh();
    }
}
