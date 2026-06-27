<?php

namespace Modules\School\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Core\Models\Concerns\BelongsToTenant;

class StudentRiskScore extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'student_id',
        'composite_score',
        'academic_score',
        'financial_score',
        'behavioral_score',
        'health_score',
        'attendance_score',
        'is_at_risk',
        'meta',
        'computed_at',
    ];

    protected function casts(): array
    {
        return [
            'is_at_risk' => 'boolean',
            'meta' => 'array',
            'computed_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Student, $this>
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
