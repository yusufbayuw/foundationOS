<?php

namespace Modules\Workflow\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Tracks an active branch spawned from a parallel/inclusive split gateway
 * inside a workflow instance. Used by WorkflowParallelCoordinator to drive
 * join-point logic across cross-step parallel branches.
 *
 * Note: the simpler "quorum at a single step" path does NOT create branch
 * rows — those are handled directly via per-assignment outcomes.
 */
class WorkflowStepBranch extends Model
{
    protected $fillable = [
        'workflow_instance_id',
        'split_step_id',
        'branch_step_id',
        'join_step_id',
        'status',
        'outcome',
        'started_at',
        'completed_at',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'meta' => 'array',
        ];
    }

    /**
     * @return BelongsTo<WorkflowInstance, $this>
     */
    public function instance(): BelongsTo
    {
        return $this->belongsTo(WorkflowInstance::class, 'workflow_instance_id');
    }

    /**
     * @return BelongsTo<WorkflowStep, $this>
     */
    public function splitStep(): BelongsTo
    {
        return $this->belongsTo(WorkflowStep::class, 'split_step_id');
    }

    /**
     * @return BelongsTo<WorkflowStep, $this>
     */
    public function branchStep(): BelongsTo
    {
        return $this->belongsTo(WorkflowStep::class, 'branch_step_id');
    }

    /**
     * @return BelongsTo<WorkflowStep, $this>
     */
    public function joinStep(): BelongsTo
    {
        return $this->belongsTo(WorkflowStep::class, 'join_step_id');
    }
}
