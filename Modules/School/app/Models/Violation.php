<?php

namespace Modules\School\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Core\Models\User;
use Modules\Core\Models\Concerns\BelongsToTenant;

class Violation extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'student_id',
        'violation_type_id',
        'reported_by',
        'handled_by',
        'date',
        'severity',
        'description',
        'location',
        'witnesses',
        'sanctions',
        'sanction_duration_days',
        'parent_notified',
        'parent_meeting_date',
        'resolution_notes',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'witnesses' => 'array',
            'sanctions' => 'array',
            'sanction_duration_days' => 'integer',
            'parent_notified' => 'boolean',
            'parent_meeting_date' => 'date',
        ];
    }
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function violationType(): BelongsTo
    {
        return $this->belongsTo(ViolationType::class);
    }

    public function reportedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function handledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by');
    }
}
