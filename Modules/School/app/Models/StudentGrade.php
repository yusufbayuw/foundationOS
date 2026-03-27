<?php

namespace Modules\School\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Core\Models\User;
use Modules\Core\Models\Concerns\BelongsToTenant;

class StudentGrade extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'student_id',
        'assessment_id',
        'score',
        'score_letter',
        'weight',
        'final_score',
        'notes',
        'is_passed',
        'graded_by',
        'graded_at',
        'is_locked',
    ];

    protected function casts(): array
    {
        return [
            'score' => 'decimal:2',
            'weight' => 'decimal:2',
            'final_score' => 'decimal:2',
            'is_passed' => 'boolean',
            'graded_at' => 'datetime',
            'is_locked' => 'boolean',
        ];
    }
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    public function gradedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'graded_by');
    }
}
