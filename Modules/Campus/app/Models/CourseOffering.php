<?php

namespace Modules\Campus\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\Models\AcademicPeriod;
use Modules\Core\Models\Concerns\BelongsToTenant;
use Modules\Core\Models\Organization;

class CourseOffering extends Model
{
    /** @use HasFactory<Factory<static>> */
    use BelongsToTenant, HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'organization_id',
        'course_id',
        'academic_period_id',
        'lecturer_id',
        'class_code',
        'capacity',
        'enrolled_count',
        'delivery_mode',
        'day_of_week',
        'start_time',
        'end_time',
        'room_name',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'capacity' => 'integer',
            'enrolled_count' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<Organization, $this>
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * @return BelongsTo<Course, $this>
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * @return BelongsTo<AcademicPeriod, $this>
     */
    public function academicPeriod(): BelongsTo
    {
        return $this->belongsTo(AcademicPeriod::class);
    }

    /**
     * @return BelongsTo<Lecturer, $this>
     */
    public function lecturer(): BelongsTo
    {
        return $this->belongsTo(Lecturer::class);
    }

    /**
     * @return HasMany<StudyPlanItem, $this>
     */
    public function studyPlanItems(): HasMany
    {
        return $this->hasMany(StudyPlanItem::class);
    }

    /**
     * @return HasMany<CourseOfferingLecturer, $this>
     */
    public function lecturerAssignments(): HasMany
    {
        return $this->hasMany(CourseOfferingLecturer::class);
    }

    /**
     * @return BelongsToMany<Lecturer, $this>
     */
    public function lecturers(): BelongsToMany
    {
        return $this->belongsToMany(Lecturer::class, 'course_offering_lecturers')
            ->withPivot(['role', 'is_active', 'assigned_at', 'removed_at'])
            ->withTimestamps();
    }
}
