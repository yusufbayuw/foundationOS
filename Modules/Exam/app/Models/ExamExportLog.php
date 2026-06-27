<?php

namespace Modules\Exam\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Core\Models\User;

class ExamExportLog extends ExamModel
{
    protected $fillable = [
        'tenant_id',
        'exam_definition_id',
        'exported_by',
        'export_type',
        'file_name',
        'row_count',
        'metadata_json',
    ];

    protected function casts(): array
    {
        return [
            'row_count' => 'integer',
            'metadata_json' => 'array',
        ];
    }

    /**
     * @return BelongsTo<ExamDefinition, $this>
     */
    public function examDefinition(): BelongsTo
    {
        return $this->belongsTo(ExamDefinition::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function exporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'exported_by');
    }
}
