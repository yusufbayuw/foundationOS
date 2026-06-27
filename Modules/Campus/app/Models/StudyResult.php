<?php

namespace Modules\Campus\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\Models\Concerns\BelongsToTenant;

class StudyResult extends Model
{
    /** @use HasFactory<Factory<static>> */
    use BelongsToTenant, HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'study_plan_item_id',
        'grade_letter',
        'grade_point',
        'weight_score',
        'components_breakdown',
        'passed',
        'published_at',
        'moodle_pulled_at',
        'notes',
        'source',
    ];

    protected function casts(): array
    {
        return [
            'grade_point' => 'decimal:2',
            'weight_score' => 'decimal:2',
            'components_breakdown' => 'array',
            'passed' => 'boolean',
            'published_at' => 'datetime',
            'moodle_pulled_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<StudyPlanItem, $this>
     */
    public function studyPlanItem(): BelongsTo
    {
        return $this->belongsTo(StudyPlanItem::class);
    }

    public function isPrintable(): bool
    {
        return $this->published_at !== null;
    }
}
