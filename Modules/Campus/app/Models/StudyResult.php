<?php

namespace Modules\Campus\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Core\Models\Tenant;

class StudyResult extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'study_plan_item_id',
        'grade_letter',
        'grade_point',
        'weight_score',
        'passed',
        'published_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'grade_point' => 'decimal:2',
            'weight_score' => 'decimal:2',
            'passed' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function studyPlanItem(): BelongsTo
    {
        return $this->belongsTo(StudyPlanItem::class);
    }
}
