<?php

namespace Modules\Exam\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Exam\Enums\QuestionDifficulty;
use Modules\Exam\Enums\QuestionStatus;
use Modules\Exam\Enums\QuestionType;

class ExamQuestion extends ExamModel
{
    use SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'exam_question_bank_id',
        'question_number',
        'type',
        'topic',
        'subtopic',
        'difficulty',
        'question_text',
        'media_path',
        'correct_answer',
        'explanation',
        'answer_key',
        'score',
        'metadata_json',
        'mi_mapping_json',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'question_number' => 'integer',
            'type' => QuestionType::class,
            'difficulty' => QuestionDifficulty::class,
            'status' => QuestionStatus::class,
            'score' => 'decimal:2',
            'metadata_json' => 'array',
            'mi_mapping_json' => 'array',
        ];
    }

    /**
     * @return BelongsTo<ExamQuestionBank, $this>
     */
    public function examQuestionBank(): BelongsTo
    {
        return $this->belongsTo(ExamQuestionBank::class);
    }

    /**
     * @return HasMany<ExamQuestionOption, $this>
     */
    public function examQuestionOptions(): HasMany
    {
        return $this->hasMany(ExamQuestionOption::class)->orderBy('sort_order');
    }

    /**
     * @return Attribute<?string, never>
     */
    protected function olympiadSubject(): Attribute
    {
        return Attribute::make(
            get: fn (): ?string => $this->metadata_json['olympiad_subject'] ?? null,
            set: function (?string $value): array {
                return ['metadata_json' => array_merge($this->metadata_json ?? [], [
                    'olympiad_subject' => $value,
                ])];
            },
        );
    }

    /**
     * @return Attribute<?string, never>
     */
    protected function olympiadLevel(): Attribute
    {
        return Attribute::make(
            get: fn (): ?string => $this->metadata_json['olympiad_level'] ?? null,
            set: function (?string $value): array {
                return ['metadata_json' => array_merge($this->metadata_json ?? [], [
                    'olympiad_level' => $value,
                ])];
            },
        );
    }

    /**
     * @return Attribute<?array<int, string>, never>
     */
    protected function skillCodes(): Attribute
    {
        return Attribute::make(
            get: fn (): ?array => $this->metadata_json['skill_codes'] ?? null,
            set: function (?array $value): array {
                return ['metadata_json' => array_merge($this->metadata_json ?? [], [
                    'skill_codes' => $value,
                ])];
            },
        );
    }

    /**
     * @return Attribute<?int, never>
     */
    protected function estimatedTimeSeconds(): Attribute
    {
        return Attribute::make(
            get: fn (): ?int => isset($this->metadata_json['estimated_time_seconds'])
                ? (int) $this->metadata_json['estimated_time_seconds']
                : null,
            set: function (?int $value): array {
                return ['metadata_json' => array_merge($this->metadata_json ?? [], [
                    'estimated_time_seconds' => $value,
                ])];
            },
        );
    }
}
