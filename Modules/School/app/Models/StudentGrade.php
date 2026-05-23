<?php

namespace Modules\School\Models;

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

class StudentGrade extends Model implements ProvidesWorkflowContext, StartsWorkflow
{
    use BelongsToTenant, HasFactory, SoftDeletes;

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

    public function workflowInstances(): MorphMany
    {
        return $this->morphMany(WorkflowInstance::class, 'subject', 'subject_type', 'subject_id');
    }

    public function workflowCode(): string
    {
        return 'student-grade-revision';
    }

    public function workflowContext(): array
    {
        return [
            'tenant_id' => $this->tenant_id,
            'student_id' => $this->student_id,
            'assessment_id' => $this->assessment_id,
            'score' => (float) $this->score,
            'final_score' => (float) $this->final_score,
            'is_locked' => (bool) $this->is_locked,
        ];
    }

    public function workflowSubjectLabel(): string
    {
        return 'Grade #'.$this->getKey();
    }

    public function workflowSubjectType(): string
    {
        return self::class;
    }
}
