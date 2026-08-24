<?php

namespace Tests\Feature;

use App\Filament\Pages\Tenancy\RegisterTenant;
use App\Models\Role;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Livewire\Livewire;
use Modules\Core\Models\Module;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\TenantModule;
use Modules\Core\Models\User;
use Modules\Core\Services\ProductProfileCatalog;
use Modules\Core\Services\TenantAdminProvisioner;
use Modules\Core\Services\TenantModuleProvisioner;
use RuntimeException;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class SelfRegistrationTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_registration_page_is_accessible(): void
    {
        $response = $this->get('/admin/register');

        $response->assertOk();
    }

    public function test_user_registration_page_renders(): void
    {
        // Filament registration uses Livewire; verify the page renders without errors
        $response = $this->get('/admin/register');

        $response->assertOk();
        $response->assertSee('Register');
    }

    public function test_verified_user_can_open_tenant_registration_wizard(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        Livewire::test(RegisterTenant::class)
            ->assertFormFieldExists('product_profile')
            ->assertSee('Profil Produk')
            ->assertSee('Regionalisasi');
    }

    public function test_unverified_user_cannot_create_a_tenant(): void
    {
        $user = User::factory()->unverified()->create();

        $this->assertFalse($user->can('create', Tenant::class));
    }

    public function test_register_tenant_page_creates_tenant_and_assigns_super_admin(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Directly invoke handleRegistration via reflection to test business logic
        $page = app(RegisterTenant::class);
        $method = new \ReflectionMethod($page, 'handleRegistration');
        $method->setAccessible(true);
        $tenant = $method->invoke($page, [
            'name' => 'Yayasan Contoh',
            'code' => 'yayasan_contoh',
            'timezone' => 'Asia/Jakarta',
            'locale' => 'id',
            'currency' => 'IDR',
            'product_profile' => 'school',
        ]);

        $this->assertNotNull($tenant, 'Tenant should be created');
        $this->assertEquals('Yayasan Contoh', $tenant->name);
        $this->assertEquals('yayasan_contoh', $tenant->code);
        $this->assertEquals($user->getKey(), $tenant->created_by);
        $this->assertSame('school', $tenant->product_profile_code);
        $this->assertSame('1.0', $tenant->product_profile_version);

        // User should be linked to the tenant via UserTenantRole
        $this->assertDatabaseHas('user_tenant_roles', [
            'user_id' => $user->getKey(),
            'tenant_id' => $tenant->getKey(),
        ]);
    }

    public function test_register_tenant_enables_k12_modules_not_marketplace_or_printing(): void
    {
        $this->syncModuleCatalog();

        $user = User::factory()->create();
        $this->actingAs($user);

        $page = app(RegisterTenant::class);
        $method = new \ReflectionMethod($page, 'handleRegistration');
        $method->setAccessible(true);
        $tenant = $method->invoke($page, [
            'name' => 'Sekolah Contoh',
            'code' => 'sekolah_contoh',
            'timezone' => 'Asia/Jakarta',
            'locale' => 'id',
            'currency' => 'IDR',
            'product_profile' => 'school',
        ]);

        $enabledCodes = TenantModule::query()
            ->where('tenant_id', $tenant->id)
            ->where('is_enabled', true)
            ->with('module')
            ->get()
            ->map(fn (TenantModule $tm) => $tm->module?->code)
            ->filter()
            ->values()
            ->all();

        $this->assertContains('school', $enabledCodes);
        $this->assertContains('enrollment', $enabledCodes);
        $this->assertNotContains('marketplace', $enabledCodes);
        $this->assertNotContains('printing', $enabledCodes);
    }

    public function test_register_tenant_uses_the_selected_product_profile(): void
    {
        $productProfiles = app(ProductProfileCatalog::class);
        $this->syncModuleCatalog($productProfiles->moduleCodes('campus'));

        $user = User::factory()->create();
        $this->actingAs($user);

        $page = app(RegisterTenant::class);
        $method = new \ReflectionMethod($page, 'handleRegistration');
        $method->setAccessible(true);
        $tenant = $method->invoke($page, [
            'name' => 'Universitas Contoh',
            'code' => 'universitas_contoh',
            'timezone' => 'Asia/Jakarta',
            'locale' => 'id',
            'currency' => 'IDR',
            'product_profile' => 'campus',
        ]);

        $enabledCodes = TenantModule::query()
            ->where('tenant_id', $tenant->id)
            ->where('is_enabled', true)
            ->with('module')
            ->get()
            ->map(fn (TenantModule $tenantModule): ?string => $tenantModule->module?->code)
            ->filter()
            ->values()
            ->all();

        $this->assertSame('campus', $tenant->product_profile_code);
        $this->assertContains('campus', $enabledCodes);
        $this->assertContains('alumni', $enabledCodes);
        $this->assertNotContains('school', $enabledCodes);
    }

    public function test_module_provisioning_enables_required_dependencies(): void
    {
        $this->syncModuleCatalog(['school', 'global']);

        Module::query()
            ->where('code', 'school')
            ->firstOrFail()
            ->update(['required_modules' => ['global']]);

        $tenant = Tenant::factory()->create();
        app(TenantModuleProvisioner::class)->enableForTenant($tenant, ['school']);

        $enabledCodes = TenantModule::query()
            ->where('tenant_id', $tenant->id)
            ->where('is_enabled', true)
            ->with('module')
            ->get()
            ->map(fn (TenantModule $tenantModule): ?string => $tenantModule->module?->code)
            ->filter()
            ->values()
            ->all();

        $this->assertEqualsCanonicalizing(['global', 'school'], $enabledCodes);
    }

    public function test_tampered_product_profile_rolls_back_registration(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $page = app(RegisterTenant::class);
        $method = new \ReflectionMethod($page, 'handleRegistration');
        $method->setAccessible(true);

        try {
            $method->invoke($page, [
                'name' => 'Tampered Tenant',
                'code' => 'tampered_tenant',
                'product_profile' => 'all_modules',
            ]);
            $this->fail('Expected InvalidArgumentException was not thrown');
        } catch (InvalidArgumentException) {
            // expected
        }

        $this->assertDatabaseMissing('tenants', ['code' => 'tampered_tenant']);
        $this->assertDatabaseCount('user_tenant_roles', 0);
    }

    public function test_register_tenant_rolls_back_when_module_provisioning_fails(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->mock(TenantModuleProvisioner::class, function ($mock): void {
            $mock->shouldReceive('enableProfileForTenant')
                ->once()
                ->andThrow(new RuntimeException('Provisioning failed'));
        });

        $page = app(RegisterTenant::class);
        $method = new \ReflectionMethod($page, 'handleRegistration');
        $method->setAccessible(true);

        $tenantCountBefore = Tenant::count();
        $membershipCountBefore = DB::table('user_tenant_roles')->count();

        try {
            $method->invoke($page, [
                'name' => 'Rollback School',
                'code' => 'rollback_school',
                'timezone' => 'Asia/Jakarta',
                'locale' => 'id',
                'currency' => 'IDR',
                'product_profile' => 'school',
            ]);
            $this->fail('Expected RuntimeException was not thrown');
        } catch (RuntimeException) {
            // expected
        }

        $this->assertSame($tenantCountBefore, Tenant::count());
        $this->assertSame($membershipCountBefore, DB::table('user_tenant_roles')->count());
        $this->assertDatabaseMissing('tenants', ['code' => 'rollback_school']);
    }

    public function test_shield_super_admin_roles_are_tenant_scoped_and_context_is_restored(): void
    {
        $tenantA = Tenant::factory()->create();
        $tenantB = Tenant::factory()->create();
        $userA = User::factory()->create();
        $userB = User::factory()->create();
        $permissionRegistrar = app(PermissionRegistrar::class);
        $permissionRegistrar->setPermissionsTeamId(0);

        $provisioner = app(TenantAdminProvisioner::class);
        $provisioner->assignShieldSuperAdmin($userA, $tenantA);
        $provisioner->assignShieldSuperAdmin($userB, $tenantB);

        $roleA = Role::query()
            ->where('name', 'super_admin')
            ->where('tenant_id', $tenantA->id)
            ->firstOrFail();
        $roleB = Role::query()
            ->where('name', 'super_admin')
            ->where('tenant_id', $tenantB->id)
            ->firstOrFail();

        $this->assertNotSame($roleA->id, $roleB->id);
        $this->assertSame(0, $permissionRegistrar->getPermissionsTeamId());
        $this->assertDatabaseHas('model_has_roles', [
            'role_id' => $roleA->id,
            'model_id' => $userA->id,
            'tenant_id' => $tenantA->id,
        ]);
        $this->assertDatabaseHas('model_has_roles', [
            'role_id' => $roleB->id,
            'model_id' => $userB->id,
            'tenant_id' => $tenantB->id,
        ]);
    }

    public function test_landing_page_shows_register_cta_for_guests(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('/admin/register');
    }

    public function test_landing_page_shows_dashboard_link_for_authenticated_users(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('/admin');
        $response->assertDontSee('/admin/register');
    }

    /**
     * @param  list<string>  $additionalModuleCodes
     */
    protected function syncModuleCatalog(array $additionalModuleCodes = []): void
    {
        foreach (array_merge(
            TenantModuleProvisioner::K12_DEFAULT_MODULE_CODES,
            $additionalModuleCodes,
            ['marketplace', 'printing'],
        ) as $code) {
            Module::query()->firstOrCreate(
                ['code' => $code],
                [
                    'slug' => $code,
                    'name' => ucfirst($code),
                    'is_core' => in_array($code, ['core', 'global'], true),
                    'is_active' => true,
                    'is_premium' => false,
                    'sort_order' => 1,
                ],
            );
        }
    }
}
