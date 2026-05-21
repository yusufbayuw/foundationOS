<?php

namespace Modules\Workflow\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Workflow\Enums\WorkflowAssignmentStatus;

class WorkflowAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'workflow_instance_id',
        'step_id',
        'assigned_to_type',
        'assigned_to_id',
        'assignment_role',
        'status',
        'outcome',
        'assigned_at',
        'claimed_at',
        'completed_at',
        'due_at',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'status' => WorkflowAssignmentStatus::class,
            'assigned_at' => 'datetime',
            'claimed_at' => 'datetime',
            'completed_at' => 'datetime',
            'due_at' => 'datetime',
            'meta' => 'array',
        ];
    }

    public function instance(): BelongsTo
    {
        return $this->belongsTo(WorkflowInstance::class, 'workflow_instance_id');
    }

    public function step(): BelongsTo
    {
        return $this->belongsTo(WorkflowStep::class, 'step_id');
    }
}
