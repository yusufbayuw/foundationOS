<?php

namespace Tests\Permission;

use App\Filament\Pages\BillingPage;
use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Gate;
use Modules\Core\Filament\Pages\BrandingSettingsPage;
use Modules\Core\Filament\Pages\SetupCenter;
use Modules\Employee\Filament\Resources\Employees\RelationManagers\DocumentsRelationManager;
use Modules\Employee\Filament\Resources\LeaveRequests\Pages\ViewLeaveRequest;
use Modules\Employee\Filament\Resources\SalarySlips\Pages\ViewSalarySlip;
use Modules\Exam\Filament\Resources\ExamDefinitions\Pages\ViewExamDefinition;
use Modules\Exam\Filament\Resources\ExamDefinitions\RelationManagers\ExamDefinitionQuestionsRelationManager;
use Modules\Exam\Filament\Resources\ExamDefinitions\RelationManagers\ExamParticipantsRelationManager;
use Modules\Finance\Filament\Resources\Budgets\Pages\ViewBudget;
use Modules\Finance\Filament\Resources\JournalEntries\Pages\ViewJournalEntry;
use Modules\Finance\Filament\Resources\Payments\Pages\ViewPayment;
use Modules\Finance\Filament\Resources\StudentInvoices\Pages\ViewStudentInvoice;
use Modules\Marketplace\Filament\Resources\MarketplaceOrders\Tables\MarketplaceOrdersTable;
use Modules\MerchOrder\Filament\Resources\MerchOrders\Tables\MerchOrdersTable;
use Modules\Monitoring\Filament\Resources\MoodleSyncOutboxes\Pages\ViewMoodleSyncOutbox;
use Modules\Monitoring\Filament\Resources\MoodleSyncOutboxes\Tables\MoodleSyncOutboxesTable;
use Modules\Procurement\Filament\Resources\PurchaseOrders\Pages\ViewPurchaseOrder;
use Modules\Procurement\Filament\Resources\PurchaseRequisitions\Pages\ViewPurchaseRequisition;
use Modules\Procurement\Filament\Resources\RfqVendors\Tables\RfqVendorsTable;
use Modules\Procurement\Filament\Resources\Vendors\Pages\ViewVendor;
use Modules\Workflow\Filament\Resources\WorkflowInstances\Pages\ViewWorkflowInstance;
use Modules\Workflow\Filament\Resources\WorkflowInstances\RelationManagers\AssignmentsRelationManager;
use Modules\Workflow\Filament\Resources\Workflows\Pages\ViewWorkflow;
use Modules\Workflow\Filament\Resources\WorkflowStepBranches\WorkflowStepBranchResource;
use Modules\Workflow\Livewire\WorkflowCanvas;
use Tests\TestCase;

class FilamentCustomSurfaceAuthorizationTest extends TestCase
{
    /**
     * @var array<class-string, array<string, string>>
     */
    private const POLICY_AUTHORIZED_ACTIONS = [
        BillingPage::class => [
            'generateInvoice' => "->authorize('create', SubscriptionLog::class)",
            'payInvoice' => "->authorize(fn (): bool => Auth::user()?->can('Update:SubscriptionLog')",
        ],
        BrandingSettingsPage::class => [
            'save' => '->authorize(fn (): bool => static::canAccess())',
        ],
        SetupCenter::class => [
            'selectProductProfile' => '->authorize(fn (): bool => static::canAccess())',
        ],
        DocumentsRelationManager::class => [
            'verify' => "->authorize('update')",
        ],
        ViewPurchaseOrder::class => [
            'approvePurchaseOrder' => "->authorize('update')",
            'rejectPurchaseOrder' => "->authorize('update')",
        ],
        ViewPayment::class => [
            'verifyPayment' => "->authorize('update')",
            'rejectPayment' => "->authorize('update')",
        ],
        ViewJournalEntry::class => [
            'postJournal' => "->authorize('update')",
            'reverseJournal' => "->authorize('update')",
        ],
        ViewLeaveRequest::class => [
            'submitForApproval' => "->authorize('update')",
            'supervisorApprove' => "->authorize('update')",
            'approve' => "->authorize('update')",
            'reject' => "->authorize('update')",
        ],
        ViewSalarySlip::class => [
            'approveSalarySlip' => "->authorize('update')",
            'markAsPaid' => "->authorize('update')",
            'sendToEmployee' => "->authorize('update')",
            'calculatePayroll' => "->authorize('update')",
        ],
        MerchOrdersTable::class => [
            'markReadyForPickup' => "->authorize('update')",
            'markPickedUp' => "->authorize('update')",
            'reject' => "->authorize('update')",
        ],
        MarketplaceOrdersTable::class => [
            'markReadyForPickup' => "->authorize('update')",
            'markPickedUp' => "->authorize('update')",
            'reject' => "->authorize('update')",
        ],
        ViewWorkflow::class => [
            'publish' => "->authorize('update')",
            'archive' => "->authorize('update')",
            'duplicateVersion' => "->authorize('replicate')",
        ],
        MoodleSyncOutboxesTable::class => [
            'retry' => "->authorize('update')",
            'retrySelected' => "->authorizeIndividualRecords('update')",
        ],
        ViewMoodleSyncOutbox::class => [
            'retry' => "->authorize('update')",
        ],
        ViewVendor::class => [
            'blacklistVendor' => "->authorize('update')",
            'removeFromBlacklist' => "->authorize('update')",
            'deactivateVendor' => "->authorize('update')",
            'activateVendor' => "->authorize('update')",
        ],
        RfqVendorsTable::class => [
            'awardToVendor' => "->authorize('update')",
        ],
        ViewStudentInvoice::class => [
            'markIssued' => "->authorize('update')",
        ],
        ViewBudget::class => [
            'startWorkflow' => "->authorize('update')",
        ],
        ViewPurchaseRequisition::class => [
            'startWorkflow' => "->authorize('update')",
        ],
        AssignmentsRelationManager::class => [
            'reassign' => "->authorize('update')",
        ],
        ViewWorkflowInstance::class => [
            'returnToStep' => '->authorize(fn (): bool => $this->hasPendingAssignment($record))',
            '$actionName' => '->authorize(fn (): bool => $this->hasPendingAssignment($record))',
        ],
        WorkflowCanvas::class => [
            'saveDraftAction' => '->authorize(fn (): bool => $this->canMutateWorkflow())',
            'publishAction' => '->authorize(fn (): bool => $this->canMutateWorkflow())',
            'importJsonAction' => "->authorize('create', Workflow::class)",
            'exportJsonAction' => '->authorize(fn (): bool => $this->canViewWorkflow())',
            'addStepAction' => '->authorize(fn (): bool => $this->canMutateWorkflow())',
            'deleteStepAction' => '->authorize(fn (): bool => $this->canMutateWorkflow())',
        ],
        ViewExamDefinition::class => [
            'markReady' => "->authorize('update')",
            'publishToRuntime' => "->authorize('publish')",
            'republishToRuntime' => "->authorize('republish')",
            'openControlRoom' => "->authorize('openControlRoom')",
            'duplicateExam' => "->authorize('replicate')",
            'closeExam' => "->authorize('update')",
            'syncParticipants' => "->authorize('syncParticipants')",
            'syncParticipantsToRuntime' => "->authorize('syncParticipants')",
            'syncAdminAccessToRuntime' => "->authorize('publish')",
            'syncResults' => "->authorize('syncResults')",
            'pushToSchoolGradebook' => "->authorize('pushToSchoolGradebook')",
            'pushToCampusGradebook' => "->authorize('pushToCampusGradebook')",
        ],
        ExamParticipantsRelationManager::class => [
            'regenerateToken' => "Gate::check('regenerateToken'",
            'exportTokensCsv' => "Gate::check('regenerateToken'",
            'generateFromClass' => "Gate::check('syncParticipants'",
            'generateFromCourseClass' => "Gate::check('syncParticipants'",
            'importCsv' => "Gate::check('syncParticipants'",
            'exportAllTokensCsv' => "Gate::check('regenerateToken'",
            'printTokenCards' => "Gate::check('regenerateToken'",
        ],
        ExamDefinitionQuestionsRelationManager::class => [
            'addQuestion' => "Gate::check('update'",
        ],
    ];

    public function test_admin_panel_uses_strict_authorization(): void
    {
        Filament::setCurrentPanel('admin');

        $this->assertTrue(Filament::getCurrentPanel()->isAuthorizationStrict());
    }

    public function test_inert_cross_step_branch_resource_is_not_exposed(): void
    {
        $this->assertFalse(WorkflowStepBranchResource::shouldRegisterNavigation());
        $this->assertFalse(WorkflowStepBranchResource::canAccess());
        $this->assertContains(
            WorkflowStepBranchResource::class,
            config('filament-shield.resources.exclude'),
        );
    }

    public function test_every_admin_resource_has_a_complete_shield_policy(): void
    {
        Filament::setCurrentPanel('admin');

        $resources = Filament::getResources();
        $requiredMethods = config('filament-shield.policies.methods');

        $this->assertNotEmpty($resources);

        foreach ($resources as $resourceClass) {
            $modelClass = $resourceClass::getModel();
            $policy = Gate::getPolicyFor($modelClass);

            $this->assertNotNull(
                $policy,
                "{$resourceClass} has no policy for {$modelClass}.",
            );

            foreach ($requiredMethods as $method) {
                $this->assertTrue(
                    method_exists($policy, $method),
                    sprintf(
                        '%s is missing %s() required by strict Filament authorization.',
                        $policy::class,
                        $method,
                    ),
                );
            }
        }
    }

    public function test_every_page_managed_by_shield_enforces_its_page_permission(): void
    {
        Filament::setCurrentPanel('admin');

        $pages = FilamentShield::getPages();

        $this->assertNotEmpty($pages);

        foreach ($pages as $pageClass => $page) {
            $this->assertContains(
                HasPageShield::class,
                class_uses_recursive($pageClass),
                "{$pageClass} is exposed in Shield without enforcing HasPageShield.",
            );

            $this->assertSame(
                'View:'.class_basename($pageClass),
                array_key_first($page['permissions']),
            );
        }
    }

    public function test_every_widget_managed_by_shield_enforces_its_widget_permission(): void
    {
        Filament::setCurrentPanel('admin');

        $widgets = FilamentShield::getWidgets();

        $this->assertNotEmpty($widgets);

        foreach ($widgets as $widgetClass => $widget) {
            $this->assertContains(
                HasWidgetShield::class,
                class_uses_recursive($widgetClass),
                "{$widgetClass} is exposed in Shield without enforcing HasWidgetShield.",
            );

            $this->assertSame(
                'View:'.class_basename($widgetClass),
                array_key_first($widget['permissions']),
            );
        }
    }

    public function test_sensitive_custom_actions_explicitly_authorize_the_resource_update_policy(): void
    {
        foreach (self::POLICY_AUTHORIZED_ACTIONS as $pageClass => $actionNames) {
            $reflection = new \ReflectionClass($pageClass);
            $fileName = $reflection->getFileName();

            $this->assertIsString($fileName);
            $source = file_get_contents($fileName);
            $this->assertIsString($source);

            foreach ($actionNames as $actionName => $authorizationFragment) {
                $actionConstructor = str_starts_with($actionName, '$')
                    ? "Action::make({$actionName})"
                    : "Action::make('{$actionName}')";
                $actionStart = strpos($source, $actionConstructor);
                $this->assertNotFalse($actionStart, "{$pageClass} is missing {$actionName}.");
                $nextAction = strpos($source, 'Action::make(', $actionStart + 1);
                $actionSource = substr(
                    $source,
                    $actionStart,
                    $nextAction === false ? null : $nextAction - $actionStart,
                );

                $this->assertStringContainsString(
                    $authorizationFragment,
                    $actionSource,
                    "{$pageClass}::{$actionName} does not enforce its Shield-backed policy.",
                );
            }
        }
    }
}
