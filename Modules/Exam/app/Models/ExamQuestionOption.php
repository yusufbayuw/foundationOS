<?php

namespace Modules\Exam\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamQuestionOption extends ExamModel
{
    protected static function booted(): void
    {
        static::creating(function (ExamQuestionOption $option): void {
            if (empty($option->tenant_id) && $option->exam_question_id) {
                $option->tenant_id = ExamQuestion::query()
                    ->withoutGlobalScopes()
                    ->whereKey($option->exam_question_id)
                    ->value('tenant_id');
            }
        });
    }

    protected $fillable = [
        'tenant_id',
        'exam_question_id',
        'option_text',
        'is_correct',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_correct' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<ExamQuestion, $this>
     */
    public function examQuestion(): BelongsTo
    {
        return $this->belongsTo(ExamQuestion::class);
    }
}
