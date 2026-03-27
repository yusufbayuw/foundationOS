<?php

namespace Modules\School\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Core\Models\Concerns\BelongsToTenant;

class AssessmentItem extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'assessment_id',
        'item_type',
        'question_number',
        'question_text',
        'question_attachment',
        'answer_options',
        'correct_answer',
        'max_score',
        'weight',
        'difficulty_level',
        'cognitive_level',
        'answer_key_rubric',
    ];

    protected function casts(): array
    {
        return [
            'question_number' => 'integer',
            'answer_options' => 'array',
            'max_score' => 'decimal:2',
            'weight' => 'decimal:2',
        ];
    }
    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    public function studentAssessmentAnswers(): HasMany
    {
        return $this->hasMany(StudentAssessmentAnswer::class);
    }
}
