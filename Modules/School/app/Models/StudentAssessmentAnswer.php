<?php

namespace Modules\School\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;

class StudentAssessmentAnswer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'assessment_id',
        'assessment_item_id',
        'student_id',
        'class_student_id',
        'answer_text',
        'answer_selected',
        'answer_attachment',
        'score',
        'max_score',
        'is_correct',
        'grader_notes',
        'graded_by',
        'graded_at',
        'attempt_number',
        'time_spent_seconds',
    ];

    protected function casts(): array
    {
        return [
            'score' => 'decimal:2',
            'max_score' => 'decimal:2',
            'is_correct' => 'boolean',
            'graded_at' => 'datetime',
            'attempt_number' => 'integer',
            'time_spent_seconds' => 'integer',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    public function assessmentItem(): BelongsTo
    {
        return $this->belongsTo(AssessmentItem::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function classStudent(): BelongsTo
    {
        return $this->belongsTo(ClassStudent::class);
    }

    public function gradedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'graded_by');
    }
}
