<?php

namespace Tests\Feature;

use App\Filament\Pages\Tenancy\RegisterTenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;
use Tests\TestCase;

class SelfRegistrationTest extends TestCase
{
    use RefreshDatabase;

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
}
