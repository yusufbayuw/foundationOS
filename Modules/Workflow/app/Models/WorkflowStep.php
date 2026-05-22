<?php

namespace Modules\Workflow\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Workflow\Enums\WorkflowAssigneeType;
use Modules\Workflow\Enums\WorkflowGatewayType;
use Modules\Workflow\Enums\WorkflowQuorumStrategy;
use Modules\Workflow\Enums\WorkflowStepType;

class WorkflowStep extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'workflow_id',
        'uuid',
        'code',
        'name',
        'description',
        'step_type',
        'gateway_type',
        'quorum_strategy',
        'quorum_value',
        'assignee_type',
        'assignee_value',
        'assignee_config',
        'form_schema',
        'action_schema',
        'sla_hours',
        'allow_reassign',
        'allow_delegate',
        'is_initial',
        'is_terminal',
        'sort_order',
        'canvas_position',
    ];

    protected function casts(): array
    {
        return [
            'canvas_position' => 'array',
            'step_type' => WorkflowStepType::class,
            'gateway_type' => WorkflowGatewayType::class,
            'quorum_strategy' => WorkflowQuorumStrategy::class,
            'quorum_value' => 'integer',
            'assignee_type' => WorkflowAssigneeType::class,
            'assignee_config' => 'array',
            'form_schema' => 'array',
            'action_schema' => 'array',
            'sla_hours' => 'integer',
            'allow_reassign' => 'boolean',
            'allow_delegate' => 'boolean',
            'is_initial' => 'boolean',
            'is_terminal' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function workflow(): BelongsTo
    {
        return $this->belongsTo(Workflow::class);
    }

    public function outgoingTransitions(): HasMany
    {
        return $this->hasMany(WorkflowTransition::class, 'from_step_id');
    }

    public function incomingTransitions(): HasMany
    {
        return $this->hasMany(WorkflowTransition::class, 'to_step_id');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(WorkflowAssignment::class, 'step_id');
    }

    public function automatedActions(): HasMany
    {
        return $this->hasMany(WorkflowAutomatedAction::class, 'step_id');
    }
}
