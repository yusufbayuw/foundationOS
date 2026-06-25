<?php

namespace Tests\Feature\Filament;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Modules\Procurement\Models\PurchaseRequisition;
use Modules\Workflow\Contracts\WorkflowInstanceStarter;
use Modules\Workflow\Contracts\WorkflowResolver;
use Modules\Workflow\Enums\WorkflowInstanceStatus;
use Modules\Workflow\Models\WorkflowInstance;
use Modules\Workflow\Services\WorkflowSubjectPageService;
use Tests\Concerns\CreatesTenantForTests;
use Tests\TestCase;

class WorkflowSubjectPageServiceTest extends TestCase
{
    use CreatesTenantForTests, RefreshDatabase;

    public function test_has_active_instance_detects_running_workflow_for_subject(): void
    {
        [$tenant, , $user, $requisition, $instance] = $this->seedRunningWorkflow();

        $service = app(WorkflowSubjectPageService::class);

        $this->assertTrue($service->hasActiveInstance($requisition));
        $this->assertSame($instance->id, $service->findActiveInstance($requisition)?->id);
    }

    public function test_start_approval_workflow_creates_running_instance(): void
    {
        [$tenant, , $user, $requisition] = $this->seedPurchaseRequisitionWithoutWorkflow();

        $this->setupProcurementPilot($tenant, $user);

        $instance = app(WorkflowSubjectPageService::class)->startApprovalWorkflow($requisition, $user, $tenant);

        $this->assertInstanceOf(WorkflowInstance::class, $instance);
        $this->assertSame(WorkflowInstanceStatus::Running, $instance->status);
        $this->assertDatabaseHas('workflow_instances', [
            'id' => $instance->id,
            'subject_type' => PurchaseRequisition::class,
            'subject_id' => $requisition->id,
            'status' => WorkflowInstanceStatus::Running->value,
        ]);
    }

    /**
     * @return array{0: \Modules\Core\Models\Tenant, 1: \Modules\Core\Models\Organization, 2: \Modules\Core\Models\User, 3: PurchaseRequisition, 4: WorkflowInstance}
     */
    private function seedRunningWorkflow(): array
    {
        [$tenant, $organization, $user, $requisition] = $this->seedPurchaseRequisitionWithoutWorkflow();

        $this->setupProcurementPilot($tenant, $user);

        $workflow = app(WorkflowResolver::class)->resolveForSubject(
            $requisition->workflowSubjectType(),
            $requisition,
            (int) $tenant->id,
            null,
        );

        $instance = app(WorkflowInstanceStarter::class)->start(
            $workflow,
            $user,
            $requisition->workflowContext(),
            $requisition,
            $user,
        );

        return [$tenant, $organization, $user, $requisition, $instance];
    }

    /**
     * @return array{0: \Modules\Core\Models\Tenant, 1: \Modules\Core\Models\Organization, 2: \Modules\Core\Models\User, 3: PurchaseRequisition}
     */
    private function seedPurchaseRequisitionWithoutWorkflow(): array
    {
        $context = $this->makeTenantContext(['core', 'procurement', 'workflow']);

        $requisition = PurchaseRequisition::create([
            'tenant_id' => $context['tenant']->id,
            'user_id' => $context['user']->id,
            'requested_by' => $context['user']->id,
            'request_number' => 'PR-WF-'.Str::upper(Str::random(4)),
            'request_date' => now()->toDateString(),
            'status' => 'draft',
            'total_estimated_amount' => 1_000_000,
        ]);

        return [$context['tenant'], $context['organization'], $context['user'], $requisition];
    }

    private function setupProcurementPilot(\Modules\Core\Models\Tenant $tenant, \Modules\Core\Models\User $user): void
    {
        $this->artisan('fos:workflow:setup-procurement-pilot', [
            'tenant' => $tenant->id,
            '--manager' => $user->id,
            '--finance' => $user->id,
            '--executive' => $user->id,
        ])->assertSuccessful();
    }
}
