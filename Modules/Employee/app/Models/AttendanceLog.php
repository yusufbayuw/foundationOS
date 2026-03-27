<?php

namespace Modules\Employee\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Core\Models\User;
use Modules\Core\Models\Concerns\BelongsToTenant;

class AttendanceLog extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'employee_id',
        'shift_id',
        'approved_by',
        'date',
        'check_in',
        'check_out',
        'work_hours',
        'overtime_hours',
        'status',
        'location_check_in',
        'location_check_out',
        'device_check_in',
        'device_check_out',
        'photo_check_in',
        'photo_check_out',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'check_in' => 'datetime',
            'check_out' => 'datetime',
            'work_hours' => 'decimal:2',
            'overtime_hours' => 'decimal:2',
            'location_check_in' => 'array',
            'location_check_out' => 'array',
        ];
    }
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
