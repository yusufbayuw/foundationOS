<?php

namespace Tests\Feature\Filament;

use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\Testing\TestAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;
use Modules\Procurement\Filament\Resources\PurchaseRequisitions\Pages\CreatePurchaseRequisition;
use Modules\Procurement\Filament\Resources\PurchaseRequisitions\Pages\EditPurchaseRequisition;
use Modules\Procurement\Filament\Resources\PurchaseRequisitions\Pages\ListPurchaseRequisitions;
use Modules\Procurement\Filament\Resources\PurchaseRequisitions\Pages\ViewPurchaseRequisition;
use Modules\Procurement\Filament\Resources\PurchaseRequisitions\PurchaseRequisitionResource;
use Modules\Procurement\Models\PurchaseRequisition;
use Modules\Workflow\Enums\WorkflowInstanceStatus;
use Modules\Workflow\Services\WorkflowSubjectPageService;
use Tests\Concerns\BootstrapsFilamentAdmin;
use Tests\Concerns\CreatesTenantForTests;
use Tests\TestCase;

class ProcurementPurchaseRequisitionFilamentTest extends TestCase
{
    use BootstrapsFilamentAdmin;
    use CreatesTenantForTests;
    use RefreshDatabase;

    private const MODULES = ['core', 'procurement', 'workflow'];

    protected function tearDown(): void
    {
        $this->tearDownFilamentAdmin();

        parent::tearDown();
    }

    public function test_list_page_renders_and_shows_tenant_records(): void
    {
        ['tenant' => $tenant, 'user' => $user] = $this->bootstrapFilamentAdmin(self::MODULES);

        $records = collect([
            $this->createPurchaseRequisition($tenant, $user, ['request_number' => 'PR-LIST-1']),
            $this->createPurchaseRequisition($tenant, $user, ['request_number' => 'PR-LIST-2']),
        ]);

        Livewire::test(ListPurchaseRequisitions::class)
            ->assertSuccessful()
            ->assertCanSeeTableRecords($records);
    }

    public function test_create_page_persists_purchase_requisition(): void
    {
        ['tenant' => $tenant, 'user' => $user] = $this->bootstrapFilamentAdmin(self::MODULES);

        Livewire::test(CreatePurchaseRequisition::class)
            ->fillForm([
                'user_id' => $user->id,
                'requested_by' => $user->id,
                'request_number' => 'PR-LW-001',
                'priority' => 'normal',
                'request_date' => now()->toDateString(),
                'required_date' => now()->addDays(7)->toDateString(),
                'total_items' => 2,
                'total_estimated_amount' => 1500000,
                'status' => 'draft',
                'justification' => 'Filament create test.',
            ])
            ->call('create')
            ->assertHasNoFormErrors()
            ->assertNotified();

        $this->assertDatabaseHas(PurchaseRequisition::class, [
            'tenant_id' => $tenant->id,
            'request_number' => 'PR-LW-001',
            'status' => 'draft',
        ]);
    }

    public function test_edit_page_updates_purchase_requisition(): void
    {
        ['tenant' => $tenant, 'user' => $user] = $this->bootstrapFilamentAdmin(self::MODULES);

        $record = $this->createPurchaseRequisition($tenant, $user, ['request_number' => 'PR-EDIT-001']);

        Livewire::test(EditPurchaseRequisition::class, ['record' => $record->getKey()])
            ->fillForm([
                'priority' => 'high',
                'notes' => 'Updated from Filament test.',
            ])
            ->call('save')
            ->assertHasNoFormErrors()
            ->assertNotified();

        $this->assertDatabaseHas(PurchaseRequisition::class, [
            'id' => $record->id,
            'priority' => 'high',
            'notes' => 'Updated from Filament test.',
        ]);
    }

    public function test_edit_page_deletes_purchase_requisition(): void
    {
        ['tenant' => $tenant, 'user' => $user] = $this->bootstrapFilamentAdmin(self::MODULES);

        $record = $this->createPurchaseRequisition($tenant, $user, ['request_number' => 'PR-DEL-001']);

        Livewire::test(EditPurchaseRequisition::class, ['record' => $record->getKey()])
            ->callAction(DeleteAction::class)
            ->assertNotified();

        $this->assertSoftDeleted(PurchaseRequisition::class, [
            'id' => $record->id,
        ]);
    }

    public function test_list_page_supports_soft_delete_bulk_action(): void
    {
        ['tenant' => $tenant, 'user' => $user] = $this->bootstrapFilamentAdmin(self::MODULES);

        $records = collect([
            $this->createPurchaseRequisition($tenant, $user, ['request_number' => 'PR-BULK-1']),
            $this->createPurchaseRequisition($tenant, $user, ['request_number' => 'PR-BULK-2']),
        ]);

        Livewire::test(ListPurchaseRequisitions::class)
            ->assertCanSeeTableRecords($records)
            ->selectTableRecords($records)
            ->callAction(TestAction::make(DeleteBulkAction::class)->table()->bulk())
            ->assertNotified();

        $records->each(function (PurchaseRequisition $record): void {
            $this->assertSoftDeleted(PurchaseRequisition::class, ['id' => $record->id]);
        });
    }

    public function test_view_page_exposes_start_workflow_action_for_draft_requisitions(): void
    {
        ['tenant' => $tenant, 'user' => $user] = $this->bootstrapFilamentAdmin(self::MODULES);

        $this->artisan('fos:workflow:setup-procurement-pilot', [
            'tenant' => $tenant->id,
            '--manager' => $user->id,
            '--finance' => $user->id,
            '--executive' => $user->id,
        ])->assertSuccessful();

        $record = $this->createPurchaseRequisition($tenant, $user, [
            'request_number' => 'PR-WF-'.Str::upper(Str::random(4)),
            'total_estimated_amount' => 2_500_000,
        ]);

        Livewire::test(ViewPurchaseRequisition::class, ['record' => $record->getKey()])
            ->assertActionVisible('startWorkflow')
            ->assertActionHidden('openWorkflow')
            ->assertActionHidden('downloadPdf');
    }

    public function test_view_page_workflow_actions_toggle_after_instance_starts(): void
    {
        ['tenant' => $tenant, 'user' => $user] = $this->bootstrapFilamentAdmin(self::MODULES);

        $this->artisan('fos:workflow:setup-procurement-pilot', [
            'tenant' => $tenant->id,
            '--manager' => $user->id,
            '--finance' => $user->id,
            '--executive' => $user->id,
        ])->assertSuccessful();

        $record = $this->createPurchaseRequisition($tenant, $user, [
            'request_number' => 'PR-WF-'.Str::upper(Str::random(4)),
            'total_estimated_amount' => 2_500_000,
        ]);

        app(WorkflowSubjectPageService::class)->startApprovalWorkflow($record, $user, $tenant);

        $this->assertDatabaseHas('workflow_instances', [
            'subject_type' => PurchaseRequisition::class,
            'subject_id' => $record->id,
            'status' => WorkflowInstanceStatus::Running->value,
        ]);

        Livewire::test(ViewPurchaseRequisition::class, ['record' => $record->getKey()])
            ->assertActionHidden('startWorkflow')
            ->assertActionVisible('openWorkflow');
    }

    public function test_view_page_pdf_action_visible_only_for_approved_requisitions(): void
    {
        ['tenant' => $tenant, 'user' => $user] = $this->bootstrapFilamentAdmin(self::MODULES);

        $draft = $this->createPurchaseRequisition($tenant, $user, [
            'request_number' => 'PR-PDF-DRAFT',
        ]);

        $approved = $this->createPurchaseRequisition($tenant, $user, [
            'request_number' => 'PR-PDF-APPROVED',
            'status' => 'approved',
        ]);

        Livewire::test(ViewPurchaseRequisition::class, ['record' => $draft->getKey()])
            ->assertActionHidden('downloadPdf');

        Livewire::test(ViewPurchaseRequisition::class, ['record' => $approved->getKey()])
            ->assertActionVisible('downloadPdf');
    }

    public function test_locked_requisition_cannot_be_edited_via_resource_gate(): void
    {
        ['tenant' => $tenant, 'user' => $user] = $this->bootstrapFilamentAdmin(self::MODULES);

        $record = $this->createPurchaseRequisition($tenant, $user, [
            'request_number' => 'PR-LOCKED',
            'status' => 'approved',
        ]);

        $this->assertFalse(PurchaseRequisitionResource::canEdit($record));
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function createPurchaseRequisition(Tenant $tenant, User $user, array $attributes = []): PurchaseRequisition
    {
        return PurchaseRequisition::create(array_merge([
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'requested_by' => $user->id,
            'request_number' => 'PR-'.Str::upper(Str::random(6)),
            'request_date' => now()->toDateString(),
            'priority' => 'normal',
            'total_items' => 1,
            'total_estimated_amount' => 100000,
            'status' => 'draft',
        ], $attributes));
    }
}
