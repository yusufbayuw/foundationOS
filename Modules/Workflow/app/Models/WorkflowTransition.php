<?php

namespace Modules\Workflow\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Workflow\Enums\WorkflowRuleType;

class WorkflowTransition extends Model
{
    /** @use HasFactory<Factory<static>> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'workflow_id',
        'from_step_id',
        'to_step_id',
        'action_name',
        'rule_type',
        'condition_rules',
        'priority',
        'is_default',
        'transition_meta',
    ];

    protected function casts(): array
    {
        return [
            'rule_type' => WorkflowRuleType::class,
            'condition_rules' => 'array',
            'priority' => 'integer',
            'is_default' => 'boolean',
            'transition_meta' => 'array',
        ];
    }

    /**
     * @return BelongsTo<Workflow, $this>
     */
    public function workflow(): BelongsTo
    {
        return $this->belongsTo(Workflow::class);
    }

    /**
     * @return BelongsTo<WorkflowStep, $this>
     */
    public function fromStep(): BelongsTo
    {
        return $this->belongsTo(WorkflowStep::class, 'from_step_id');
    }

    /**
     * @return BelongsTo<WorkflowStep, $this>
     */
    public function toStep(): BelongsTo
    {
        return $this->belongsTo(WorkflowStep::class, 'to_step_id');
    }
}
