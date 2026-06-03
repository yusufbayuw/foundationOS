<?php

namespace Tests\Feature;

use App\Support\CurrentTenant;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Livewire\Livewire;
use Modules\Ai\Filament\Resources\AiPromptTemplates\Pages\CreateAiPromptTemplate;
use Modules\Ai\Filament\Resources\AiPromptTemplates\Pages\ListAiPromptTemplates;
use Modules\Ai\Models\AiPromptTemplate;
use Modules\Core\Models\Organization;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\TenantModule;
use Modules\Core\Models\TenantRole;
use Modules\Core\Models\User;
use Modules\Core\Models\UserTenantRole;
use Modules\Core\Services\ApplicationModuleCatalog;
use Modules\Core\Services\TenantModuleProvisioner;
use Tests\Concerns\CreatesTenantForTests;
use Tests\TestCase;

class AiPromptTemplateFilamentTest extends TestCase
{
    use CreatesTenantForTests;
    use LazilyRefreshDatabase;

    protected function tearDown(): void
    {
        Filament::setTenant(null);
        app(CurrentTenant::class)->forget();

        parent::tearDown();
    }

    public function test_list_ai_prompt_templates_page_renders_for_tenant(): void
    {
        $this->bootstrapFilament(['core', 'ai']);

        Livewire::test(ListAiPromptTemplates::class)
            ->assertSuccessful();
    }

    public function test_create_ai_prompt_template_normalizes_code_via_registration_service(): void
    {
        ['tenant' => $tenant] = $this->bootstrapFilament(['core', 'ai']);

        Livewire::test(CreateAiPromptTemplate::class)
            ->fillForm([
                'code' => ' advisor_help ',
                'name' => 'Advisor help draft',
                'status' => 'active',
            ])
            ->call('create')
            ->assertHasNoFormErrors()
            ->assertNotified();

        $this->assertDatabaseHas(AiPromptTemplate::class, [
            'tenant_id' => $tenant->id,
            'code' => 'ADVISOR_HELP',
            'name' => 'Advisor help draft',
        ]);
    }

    /**
     * @param  list<string>  $modules
     * @return array{tenant: Tenant, organization: Organization, user: User, role: TenantRole}
     */
    protected function bootstrapFilament(array $modules): array
    {
        $context = $this->makeTenantContext($modules);
        $superAdmin = User::factory()->superAdmin()->create();

        UserTenantRole::create([
            'user_id' => $superAdmin->id,
            'tenant_id' => $context['tenant']->id,
            'organization_id' => $context['organization']->id,
            'tenant_role_id' => $context['role']->id,
            'assigned_by' => $superAdmin->id,
            'is_primary' => true,
        ]);

        app(ApplicationModuleCatalog::class)->sync();
        app(TenantModuleProvisioner::class)->enableForTenant($context['tenant'], $modules);

        TenantModule::query()
            ->where('tenant_id', $context['tenant']->id)
            ->update(['is_enabled' => true]);

        Filament::setCurrentPanel('admin');
        $this->actingAs($superAdmin);
        app(CurrentTenant::class)->set($context['tenant']);
        Filament::setTenant($context['tenant']);

        return array_merge($context, ['user' => $superAdmin]);
    }
}
