<?php

namespace Tests\Feature;

use App\Filament\Pages\Tenancy\RegisterTenant;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\DB;
use Modules\Core\Models\Module;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\TenantModule;
use Modules\Core\Models\User;
use Modules\Core\Services\TenantModuleProvisioner;
use RuntimeException;
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
        ]);

        $this->assertNotNull($tenant, 'Tenant should be created');
        $this->assertEquals('Yayasan Contoh', $tenant->name);
        $this->assertEquals('yayasan_contoh', $tenant->code);
        $this->assertEquals($user->getKey(), $tenant->created_by);

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

    public function test_register_tenant_rolls_back_when_module_provisioning_fails(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->mock(TenantModuleProvisioner::class, function ($mock): void {
            $mock->shouldReceive('enableForTenant')
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
            ]);
            $this->fail('Expected RuntimeException was not thrown');
        } catch (RuntimeException) {
            // expected
        }

        $this->assertSame($tenantCountBefore, Tenant::count());
        $this->assertSame($membershipCountBefore, DB::table('user_tenant_roles')->count());
        $this->assertDatabaseMissing('tenants', ['code' => 'rollback_school']);
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

    protected function syncModuleCatalog(): void
    {
        foreach (array_merge(
            TenantModuleProvisioner::K12_DEFAULT_MODULE_CODES,
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
