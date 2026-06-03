<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Models\Tenant;
use Modules\School\Models\StudentGrade;
use Modules\Workflow\Models\Workflow;
use Modules\Workflow\Models\WorkflowStep;

class SetupStudentGradeRevisionWorkflowCommand extends Command
{
    protected $signature = 'fos:workflow:setup-student-grade-revision {tenant} {--homeroom=} {--principal=}';

    protected $description = 'Create student grade revision approval workflow (homeroom → principal)';

    public function handle(): int
    {
        $tenantId = (int) $this->argument('tenant');
        $tenant = Tenant::query()->findOrFail($tenantId);
        $homeroom = (int) ($this->option('homeroom') ?: 0);
        $principal = (int) ($this->option('principal') ?: 0);

        if ($homeroom <= 0 || $principal <= 0) {
            $this->error('Provide --homeroom= and --principal= user IDs.');

            return self::FAILURE;
        }

        $workflow = Workflow::withoutTenantScope()->updateOrCreate(
            [
                'tenant_id' => $tenant->id,
                'organization_id' => null,
                'code' => 'student-grade-revision',
            ],
            [
                'name' => 'Student Grade Revision',
                'subject_type' => StudentGrade::class,
                'version' => 1,
                'is_active' => true,
                'description' => 'Approval for grade changes after report card lock',
            ],
        );

        WorkflowStep::query()->where('workflow_id', $workflow->id)->delete();

        WorkflowStep::query()->create([
            'workflow_id' => $workflow->id,
            'tenant_id' => $tenant->id,
            'name' => 'Homeroom Teacher Review',
            'step_order' => 1,
            'assignee_type' => 'user',
            'assignee_id' => $homeroom,
            'sla_hours' => 48,
        ]);

        WorkflowStep::query()->create([
            'workflow_id' => $workflow->id,
            'tenant_id' => $tenant->id,
            'name' => 'Principal Approval',
            'step_order' => 2,
            'assignee_type' => 'user',
            'assignee_id' => $principal,
            'sla_hours' => 72,
        ]);

        $this->info("Workflow #{$workflow->id} ready for tenant {$tenant->name}.");

        return self::SUCCESS;
    }
}
