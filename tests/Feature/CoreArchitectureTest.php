<?php

namespace Tests\Feature;

use App\Support\CurrentTenant;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Modules\Core\Models\Organization;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\TenantRole;
use Modules\Core\Models\User;
use Modules\Core\Scopes\TenantScope;
use Modules\Core\Support\Tenancy\CurrentTenant as CoreCurrentTenant;
use Tests\TestCase;

class CoreArchitectureTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_tenancy_primitives_live_in_core_module_with_app_aliases(): void
    {
        $this->assertTrue(is_subclass_of(CurrentTenant::class, CoreCurrentTenant::class));
        $this->assertTrue(is_subclass_of(\App\Scopes\TenantScope::class, TenantScope::class));
        $this->assertSame(
            app(CoreCurrentTenant::class),
            app(CurrentTenant::class),
        );
    }

    public function test_tenant_model_only_imports_core_modules_in_its_primary_file(): void
    {
        $source = file_get_contents(base_path('Modules/Core/app/Models/Tenant.php'));

        $this->assertStringNotContainsString('Modules\\School\\', $source);
        $this->assertStringNotContainsString('Modules\\Finance\\', $source);
        $this->assertStringContainsString('HasLegacyTenantDomainRelations', $source);
    }

    public function test_legacy_tenant_relations_remain_available_for_backward_compatibility(): void
    {
        $this->assertTrue(method_exists(Tenant::class, 'students'));
        $this->assertTrue(method_exists(Tenant::class, 'budgets'));
    }

    public function test_user_legacy_relations_remain_available_for_backward_compatibility(): void
    {
        $this->assertTrue(method_exists(User::class, 'students'));
        $this->assertTrue(method_exists(User::class, 'verifiedPayments'));
    }

    public function test_organization_legacy_relations_remain_available_for_backward_compatibility(): void
    {
        $this->assertTrue(method_exists(Organization::class, 'students'));
        $this->assertTrue(method_exists(Organization::class, 'budgets'));
    }

    public function test_tenant_role_stays_a_focused_core_model(): void
    {
        $source = file_get_contents(base_path('Modules/Core/app/Models/TenantRole.php'));

        $this->assertLessThan(60, substr_count($source, "\n"));
        $this->assertStringContainsString('function userTenantRoles', $source);
        $this->assertStringNotContainsString('HasLegacy', $source);
    }
}
