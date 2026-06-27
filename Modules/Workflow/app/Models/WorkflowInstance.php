<?php

namespace Modules\Workflow\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\Models\Concerns\BelongsToTenant;
use Modules\Core\Models\Organization;
use Modules\Core\Models\User;
use Modules\Workflow\Enums\WorkflowInstanceStatus;

class WorkflowInstance extends Model
{
    /** @use HasFactory<Factory<static>> */
    use BelongsToTenant, HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'organization_id',
        'workflow_id',
        'workflow_version',
        'workflow_snapshot',
        'current_step_id',
        'requester_id',
        'started_by',
        'subject_type',
        'subject_id',
        'subject_label',
        'context_data',
        'form_data',
        'computed_data',
        'status',
        'current_assignees',
        'started_at',
        'due_at',
        'completed_at',
        'cancelled_at',
        'rejected_at',
    ];

    protected function casts(): array
    {
        return [
            'workflow_version' => 'integer',
            'workflow_snapshot' => 'array',
            'context_data' => 'array',
            'form_data' => 'array',
            'computed_data' => 'array',
            'status' => WorkflowInstanceStatus::class,
            'current_assignees' => 'array',
            'started_at' => 'datetime',
            'due_at' => 'datetime',
            'completed_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'rejected_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Organization, $this>
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
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
    public function currentStep(): BelongsTo
    {
        return $this->belongsTo(WorkflowStep::class, 'current_step_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function starter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'started_by');
    }

    /**
     * @return MorphTo<Model, $this>
     */
    public function subject(): MorphTo
    {
        return $this->morphTo(__FUNCTION__, 'subject_type', 'subject_id');
    }

    /**
     * @return HasMany<WorkflowInstanceLog, $this>
     */
    public function logs(): HasMany
    {
        return $this->hasMany(WorkflowInstanceLog::class);
    }

    /**
     * @return HasMany<WorkflowAssignment, $this>
     */
    public function assignments(): HasMany
    {
        return $this->hasMany(WorkflowAssignment::class);
    }

    /**
     * @return HasMany<WorkflowEvidence, $this>
     */
    public function evidences(): HasMany
    {
        return $this->hasMany(WorkflowEvidence::class);
    }
}
