<?php

namespace Modules\Campus\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Core\Models\Concerns\BelongsToTenant;

class CoursePrerequisite extends Model
{
    use BelongsToTenant;

    protected $table = 'course_prerequisites';

    protected $fillable = [
        'tenant_id',
        'course_id',
        'prerequisite_course_id',
        'min_grade',
        'is_strict',
        'is_required',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'min_grade' => 'decimal:2',
            'is_strict' => 'boolean',
            'is_required' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<Course, $this>
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * @return BelongsTo<Course, $this>
     */
    public function prerequisiteCourse(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'prerequisite_course_id');
    }
}
