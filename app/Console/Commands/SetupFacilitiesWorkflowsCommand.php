<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Facility\Models\RoomBooking;
use Modules\Legal\Models\Contract;
use Modules\Workflow\Enums\WorkflowDefinitionStatus;
use Modules\Workflow\Models\Workflow;
use Modules\Workflow\Models\WorkflowStep;
use Modules\Workflow\Models\WorkflowTransition;
use Modules\Workflow\Services\WorkflowDefinitionLifecycleService;

class SetupFacilitiesWorkflowsCommand extends Command
{
    protected $signature = 'fos:workflow:setup-facilities {tenant : Tenant ID} {--approver= : User ID for approval steps}';

    protected $description = 'Create default workflows for Legal contracts and Facility room bookings';

    public function handle(WorkflowDefinitionLifecycleService $lifecycle): int
    {
        $tenantId = (int) $this->argument('tenant');
        $approverId = (int) ($this->option('approver') ?: 1);

        $this->createWorkflow(
            $lifecycle,
            $tenantId,
            'contract-approval',
            'Contract Approval',
            Contract::class,
            'Legal',
            $approverId,
        );

        $this->createWorkflow(
            $lifecycle,
            $tenantId,
            'room-booking-approval',
            'Room Booking Approval',
            RoomBooking::class,
            'Facility',
            $approverId,
        );

        $this->info('Facilities workflows created (draft). Publish via Workflow admin when ready.');

        return self::SUCCESS;
    }

    protected function createWorkflow(
        WorkflowDefinitionLifecycleService $lifecycle,
        int $tenantId,
        string $code,
        string $name,
        string $subjectType,
        string $module,
        int $approverId,
    ): void {
        if (Workflow::query()->where('tenant_id', $tenantId)->where('code', $code)->exists()) {
            return;
        }

        $workflow = Workflow::query()->create([
            'tenant_id' => $tenantId,
            'code' => $code,
            'name' => $name,
            'module' => $module,
            'subject_type' => $subjectType,
            'trigger_mode' => 'manual',
            'version' => 1,
            'status' => WorkflowDefinitionStatus::Draft,
            'is_active' => false,
            'created_by' => $approverId,
            'updated_by' => $approverId,
        ]);

        $step = WorkflowStep::query()->create([
            'workflow_id' => $workflow->getKey(),
            'code' => 'approval',
            'name' => 'Approval',
            'assignee_user_id' => $approverId,
            'sort_order' => 1,
            'is_initial' => true,
        ]);

        WorkflowTransition::query()->create([
            'workflow_id' => $workflow->getKey(),
            'from_step_id' => $step->getKey(),
            'to_step_id' => null,
            'code' => 'approve',
            'name' => 'Approve',
            'sort_order' => 1,
        ]);
    }
}
