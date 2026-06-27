<?php

namespace Modules\Employee\Models;

use App\Support\TypedValue;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\Models\Concerns\BelongsToTenant;
use Modules\Core\Models\User;
use Modules\Workflow\Contracts\ProvidesWorkflowContext;
use Modules\Workflow\Contracts\StartsWorkflow;
use Modules\Workflow\Models\WorkflowInstance;

class LeaveRequest extends Model implements ProvidesWorkflowContext, StartsWorkflow
{
    /** @use HasFactory<Factory<static>> */
    use BelongsToTenant, HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'employee_id',
        'substitute_employee_id',
        'supervisor_id',
        'approver_id',
        'leave_type',
        'start_date',
        'end_date',
        'total_days',
        'reason',
        'attachment',
        'status',
        'supervisor_approved_at',
        'approved_at',
        'rejection_reason',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'total_days' => 'integer',
            'supervisor_approved_at' => 'datetime',
            'approved_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Employee, $this>
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * @return BelongsTo<Employee, $this>
     */
    public function substituteEmployee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'substitute_employee_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    /**
     * @return MorphMany<WorkflowInstance, $this>
     */
    public function workflowInstances(): MorphMany
    {
        return $this->morphMany(WorkflowInstance::class, 'subject', 'subject_type', 'subject_id');
    }

    public function workflowCode(): string
    {
        return 'leave-request-approval';
    }

    public function workflowContext(): array
    {
        return [
            'tenant_id' => $this->tenant_id,
            'employee_id' => $this->employee_id,
            'leave_type' => $this->leave_type,
            'total_days' => $this->total_days,
            'start_date' => $this->start_date->toDateString(),
            'end_date' => $this->end_date->toDateString(),
            'status' => $this->status,
        ];
    }

    public function workflowSubjectLabel(): string
    {
        return 'Cuti #'.TypedValue::string($this->getKey()).' — '.($this->employee->full_name ?? '');
    }

    public function workflowSubjectType(): string
    {
        return self::class;
    }

    public function isPrintable(): bool
    {
        return (string) $this->status === 'approved';
    }
}
