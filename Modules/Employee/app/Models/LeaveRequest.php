<?php

namespace Modules\Employee\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Core\Models\User;
use Modules\Core\Models\Concerns\BelongsToTenant;
use Modules\Employee\Enums\LeaveRequestStatus;
use Modules\Employee\Enums\LeaveType;

class LeaveRequest extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

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
            'leave_type' => LeaveType::class,
            'status' => LeaveRequestStatus::class,
            'start_date' => 'date',
            'end_date' => 'date',
            'total_days' => 'integer',
            'supervisor_approved_at' => 'datetime',
            'approved_at' => 'datetime',
        ];
    }

    public function isLockedForMutation(): bool
    {
        return $this->status?->isLockedForMutation() ?? false;
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function substituteEmployee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'substitute_employee_id');
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approver_id');
    }
}
