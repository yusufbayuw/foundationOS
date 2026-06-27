<?php

namespace Modules\School\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\Models\Concerns\BelongsToTenant;

class AssessmentItem extends Model
{
    /** @use HasFactory<Factory<static>> */
    use BelongsToTenant, HasFactory, SoftDeletes;

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

    /**
     * @return BelongsTo<Assessment, $this>
     */
    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    /**
     * @return HasMany<StudentAssessmentAnswer, $this>
     */
    public function studentAssessmentAnswers(): HasMany
    {
        return $this->hasMany(StudentAssessmentAnswer::class);
    }
}
