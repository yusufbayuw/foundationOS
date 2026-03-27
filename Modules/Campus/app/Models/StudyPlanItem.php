<?php

namespace Modules\Campus\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Modules\Core\Models\Tenant;

class StudyPlanItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'study_plan_id',
        'course_offering_id',
        'course_id',
        'credits',
        'status',
        'grade_letter',
        'grade_point',
        'remarks',
    ];

    protected function casts(): array
    {
        return [
            'credits' => 'integer',
            'grade_point' => 'decimal:2',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function studyPlan(): BelongsTo
    {
        return $this->belongsTo(StudyPlan::class);
    }

    public function courseOffering(): BelongsTo
    {
        return $this->belongsTo(CourseOffering::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function studyResult(): HasOne
    {
        return $this->hasOne(StudyResult::class);
    }
}
