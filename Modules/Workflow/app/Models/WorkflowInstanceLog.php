<?php

namespace Modules\Workflow\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Core\Models\User;
use Modules\Workflow\Enums\WorkflowLogType;

class WorkflowInstanceLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'workflow_instance_id',
        'step_id',
        'transition_id',
        'actor_id',
        'log_type',
        'action_taken',
        'status_before',
        'status_after',
        'payload_before',
        'payload_after',
        'form_data_snapshot',
        'notes',
        'ip_address',
        'user_agent',
        'request_id',
        'logged_at',
    ];

    protected function casts(): array
    {
        return [
            'log_type' => WorkflowLogType::class,
            'payload_before' => 'array',
            'payload_after' => 'array',
            'form_data_snapshot' => 'array',
            'logged_at' => 'datetime',
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

    public function transition(): BelongsTo
    {
        return $this->belongsTo(WorkflowTransition::class, 'transition_id');
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }
}
