<?php

namespace Modules\School\Observers;

use Modules\School\Models\StudentGrade;
use Modules\Workflow\Contracts\WorkflowInstanceStarter;
use Modules\Workflow\Contracts\WorkflowResolver;
use Modules\Workflow\Enums\WorkflowInstanceStatus;
use Modules\Workflow\Models\WorkflowInstance;

class StudentGradeObserver
{
    public function updating(StudentGrade $grade): void
    {
        if (! $grade->is_locked) {
            return;
        }

        $dirty = collect($grade->getDirty())->only(['score', 'final_score', 'score_letter', 'is_passed']);
        if ($dirty->isEmpty()) {
            return;
        }

        $hasOpenInstance = WorkflowInstance::query()
            ->where('subject_type', StudentGrade::class)
            ->where('subject_id', $grade->getKey())
            ->where('status', WorkflowInstanceStatus::Running->value)
            ->exists();

        if ($hasOpenInstance) {
            return;
        }

        try {
            $workflow = app(WorkflowResolver::class)->resolveForSubject(
                $grade::class,
                $grade,
                (int) $grade->tenant_id,
                null,
            );
        } catch (\Throwable) {
            return;
        }

        $starter = app(WorkflowInstanceStarter::class);
        $user = auth()->user();
        if (! $user) {
            return;
        }

        $starter->start(
            $workflow,
            $user,
            array_merge($grade->workflowContext(), [
                'revision_reason' => $grade->notes,
                'changed_fields' => $dirty->keys()->all(),
            ]),
            $grade,
            $user,
        );
    }
}
