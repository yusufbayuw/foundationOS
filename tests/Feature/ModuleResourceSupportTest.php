<?php

namespace Tests\Feature;

use Filament\Facades\Filament;
use Filament\Support\Icons\Heroicon;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Modules\Core\Filament\Resources\Organizations\OrganizationResource;
use Modules\Core\Filament\Support\Guards\GlobalResourceGuard;
use Modules\Core\Filament\Support\Labels\ResourceLabelResolver;
use Modules\Core\Filament\Support\Navigation\ModuleVisibility;
use Modules\Core\Filament\Support\Navigation\NavigationIconResolver;
use Modules\Core\Filament\Support\Navigation\NavigationSortRegistry;
use Modules\Core\Filament\Support\Records\RecordTitleResolver;
use Modules\Core\Models\Module;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\TenantModule;
use Modules\Core\Models\User;
use Modules\School\Filament\Resources\Students\StudentResource;
use Modules\School\Models\Student;
use Tests\TestCase;

class ModuleResourceSupportTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function tearDown(): void
    {
        Filament::setTenant(null);

        parent::tearDown();
    }

    public function test_navigation_icon_resolver_preserves_existing_resource_name_mapping(): void
    {
        $this->assertSame(Heroicon::UserGroup, NavigationIconResolver::resolve(StudentResource::class));
        $this->assertSame(Heroicon::BuildingOffice2, NavigationIconResolver::resolve(OrganizationResource::class));
        $this->assertSame(Heroicon::Squares2x2, NavigationIconResolver::resolve(UnknownResource::class));
    }

    public function test_navigation_sort_registry_preserves_existing_sort_map(): void
    {
        $this->assertSame(10, NavigationSortRegistry::sortFor('Core', 'OrganizationResource'));
        $this->assertSame(50, NavigationSortRegistry::sortFor('School', 'StudentResource'));
        $this->assertNull(NavigationSortRegistry::sortFor('School', 'MissingResource'));
    }

    public function test_module_visibility_preserves_core_global_and_tenant_module_rules(): void
    {
        $tenant = $this->makeTenant();
        $user = User::factory()->create();

        $schoolModule = Module::create([
            'code' => 'school',
            'slug' => 'school',
            'name' => 'School',
            'is_core' => false,
            'is_active' => true,
        ]);

        $this->actingAs($user);
        Filament::setTenant($tenant);

        $this->assertTrue(ModuleVisibility::shouldRegisterNavigation('Core'));
        $this->assertTrue(ModuleVisibility::shouldRegisterNavigation('Global'));
        $this->assertFalse(ModuleVisibility::shouldRegisterNavigation('School'));

        TenantModule::create([
            'tenant_id' => $tenant->id,
            'module_id' => $schoolModule->id,
            'is_enabled' => true,
        ]);

        Cache::forget(ModuleVisibility::enabledModulesCacheKey($tenant->id));

        $this->assertTrue(ModuleVisibility::shouldRegisterNavigation('School'));
    }

    public function test_label_and_record_title_resolvers_preserve_existing_fallbacks(): void
    {
        $this->assertSame('Tahun Akademik', ResourceLabelResolver::navigationLabel('academic years'));
        $this->assertSame('Tahun Akademik', ResourceLabelResolver::pluralModelLabel('academic years'));

        $student = new Student(['nis' => 'NIS-001']);
        $this->assertSame('NIS-001', RecordTitleResolver::resolve($student));

        $studentWithoutAttributes = new Student;
        $studentWithoutAttributes->setRelation('user', new User(['name' => 'Jane Student']));

        $this->assertSame('Jane Student', RecordTitleResolver::resolve($studentWithoutAttributes));
        $this->assertNull(RecordTitleResolver::resolve(null));
    }

    public function test_global_resource_guard_preserves_existing_mutation_gate_logic(): void
    {
        $regularUser = User::factory()->create();
        $superAdmin = User::factory()->superAdmin()->create();

        $this->assertFalse(GlobalResourceGuard::isMutationRestricted(true));
        $this->assertTrue(GlobalResourceGuard::isMutationRestricted(false));
        $this->assertFalse(GlobalResourceGuard::canCurrentUserMutate());

        $this->actingAs($regularUser);
        $this->assertFalse(GlobalResourceGuard::canCurrentUserMutate());

        $this->actingAs($superAdmin);
        $this->assertTrue(GlobalResourceGuard::canCurrentUserMutate());
    }

    private function makeTenant(): Tenant
    {
        $plan = SubscriptionPlan::create([
            'code' => 'module-resource-support',
            'name' => 'Module Resource Support',
            'included_modules' => ['core'],
        ]);

        return Tenant::create([
            'uuid' => fake()->uuid(),
            'code' => 'module-resource-support',
            'name' => 'Module Resource Support',
            'subscription_plan_id' => $plan->id,
        ]);
    }
}

class UnknownResource {}
