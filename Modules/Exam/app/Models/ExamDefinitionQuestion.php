<?php

namespace Modules\Exam\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Modules\Exam\Models\Concerns\HasExamUuid;

class ExamDefinitionQuestion extends Pivot
{
    use HasExamUuid;

    protected $table = 'exam_definition_questions';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'tenant_id',
        'exam_definition_id',
        'exam_question_id',
        'sort_order',
        'score_override',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'score_override' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (ExamDefinitionQuestion $pivot): void {
            if (empty($pivot->tenant_id) && $pivot->exam_definition_id) {
                $pivot->tenant_id = ExamDefinition::query()
                    ->withoutGlobalScopes()
                    ->whereKey($pivot->exam_definition_id)
                    ->value('tenant_id');
            }
        });
    }

    /**
     * @return BelongsTo<ExamDefinition, $this>
     */
    public function examDefinition(): BelongsTo
    {
        return $this->belongsTo(ExamDefinition::class);
    }

    /**
     * @return BelongsTo<ExamQuestion, $this>
     */
    public function examQuestion(): BelongsTo
    {
        return $this->belongsTo(ExamQuestion::class);
    }

    public function effectiveScore(): float
    {
        if ($this->score_override !== null) {
            return (float) $this->score_override;
        }

        return (float) ($this->examQuestion?->score ?? 0);
    }
}
