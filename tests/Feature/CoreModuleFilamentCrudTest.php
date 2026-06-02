<?php

namespace Tests\Feature;

use App\Support\CurrentTenant;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Livewire\Livewire;
use Modules\Core\Models\TenantModule;
use Modules\Core\Models\User;
use Modules\Core\Models\UserTenantRole;
use Modules\Core\Services\ApplicationModuleCatalog;
use Modules\Core\Services\TenantModuleProvisioner;
use Modules\Enrollment\Filament\Resources\Applicants\Pages\ListApplicants;
use Modules\Finance\Filament\Resources\StudentInvoices\Pages\CreateStudentInvoice;
use Modules\Finance\Filament\Resources\StudentInvoices\Pages\ListStudentInvoices;
use Modules\Finance\Models\StudentInvoice;
use Modules\School\Filament\Resources\Students\Pages\ListStudents;
use Tests\Concerns\CreatesTenantForTests;
use Tests\TestCase;

class CoreModuleFilamentCrudTest extends TestCase
{
    use CreatesTenantForTests;
    use LazilyRefreshDatabase;

    protected function tearDown(): void
    {
        Filament::setTenant(null);
        app(CurrentTenant::class)->forget();

        parent::tearDown();
    }

    public function test_list_applicants_page_renders_for_tenant(): void
    {
        $this->runFilamentListTest(ListApplicants::class, ['core', 'enrollment']);
    }

    public function test_list_student_invoices_page_renders_for_tenant(): void
    {
        $this->runFilamentListTest(ListStudentInvoices::class, ['core', 'finance']);
    }

    public function test_list_students_page_renders_for_tenant(): void
    {
        $this->runFilamentListTest(ListStudents::class, ['core', 'school']);
    }

    public function test_create_student_invoice_persists_record(): void
    {
        ['tenant' => $tenant, 'user' => $user] = $this->bootstrapFilament(['core', 'finance']);

        Livewire::test(CreateStudentInvoice::class)
            ->fillForm([
                'invoice_number' => 'INV-LW-001',
                'issue_date' => now()->toDateString(),
                'due_date' => now()->addDays(14)->toDateString(),
                'status' => 'draft',
                'amount' => 100000,
                'discount_amount' => 0,
                'penalty_amount' => 0,
                'total_amount' => 100000,
                'paid_amount' => 0,
                'remaining_amount' => 100000,
            ])
            ->call('create')
            ->assertHasNoFormErrors()
            ->assertNotified();

        $this->assertDatabaseHas(StudentInvoice::class, [
            'tenant_id' => $tenant->id,
            'invoice_number' => 'INV-LW-001',
            'status' => 'draft',
        ]);
    }

    /**
     * @param  class-string  $pageClass
     * @param  list<string>  $modules
     */
    protected function runFilamentListTest(string $pageClass, array $modules): void
    {
        $this->bootstrapFilament($modules);

        Livewire::test($pageClass)
            ->assertSuccessful();
    }

    /**
     * @param  list<string>  $modules
     * @return array{tenant: \Modules\Core\Models\Tenant, organization: \Modules\Core\Models\Organization, user: User, role: \Modules\Core\Models\TenantRole}
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

        app(CurrentTenant::class)->set($context['tenant']);
        Filament::setTenant($context['tenant']);
        Filament::setCurrentPanel('admin');
        $this->actingAs($superAdmin);

        return array_merge($context, ['user' => $superAdmin]);
    }
}
