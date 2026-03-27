<?php

namespace Modules\Workflow\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Workflow\Enums\WorkflowAutomationActionType;
use Modules\Workflow\Enums\WorkflowAutomationTrigger;

class WorkflowAutomatedAction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'workflow_id',
        'step_id',
        'trigger_event',
        'action_type',
        'name',
        'config',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'trigger_event' => WorkflowAutomationTrigger::class,
            'action_type' => WorkflowAutomationActionType::class,
            'config' => 'array',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function workflow(): BelongsTo
    {
        return $this->belongsTo(Workflow::class);
    }

    public function step(): BelongsTo
    {
        return $this->belongsTo(WorkflowStep::class, 'step_id');
    }
}
