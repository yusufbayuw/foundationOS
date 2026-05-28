<?php

namespace Modules\Exam\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExamPackage extends ExamModel
{
    use SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'exam_definition_id',
        'exam_question_bank_id',
        'name',
        'code',
        'sort_order',
        'settings_json',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'settings_json' => 'array',
        ];
    }

    public function examDefinition(): BelongsTo
    {
        return $this->belongsTo(ExamDefinition::class);
    }

    public function examQuestionBank(): BelongsTo
    {
        return $this->belongsTo(ExamQuestionBank::class);
    }
}
