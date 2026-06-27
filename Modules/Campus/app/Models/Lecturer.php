<?php

namespace Modules\Campus\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\Models\Concerns\BelongsToTenant;
use Modules\Core\Models\Organization;
use Modules\Core\Models\User;

class Lecturer extends Model
{
    /** @use HasFactory<Factory<static>> */
    use BelongsToTenant, HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'organization_id',
        'user_id',
        'study_program_id',
        'nidn',
        'employee_number',
        'full_name',
        'academic_title_prefix',
        'academic_title_suffix',
        'functional_position',
        'employment_status',
        'email',
        'phone',
        'join_date',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'join_date' => 'date',
            'is_active' => 'boolean',
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
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<StudyProgram, $this>
     */
    public function studyProgram(): BelongsTo
    {
        return $this->belongsTo(StudyProgram::class);
    }

    /**
     * @return HasMany<CourseOffering, $this>
     */
    public function courseOfferings(): HasMany
    {
        return $this->hasMany(CourseOffering::class);
    }

    /**
     * @return BelongsToMany<CourseOffering, $this>
     */
    public function taughtCourseOfferings(): BelongsToMany
    {
        return $this->belongsToMany(CourseOffering::class, 'course_offering_lecturers')
            ->withPivot(['role', 'is_active', 'assigned_at', 'removed_at'])
            ->withTimestamps();
    }

    /**
     * @return HasMany<CourseOfferingLecturer, $this>
     */
    public function courseOfferingAssignments(): HasMany
    {
        return $this->hasMany(CourseOfferingLecturer::class);
    }

    /**
     * @return HasMany<StudyProgram, $this>
     */
    public function headedStudyPrograms(): HasMany
    {
        return $this->hasMany(StudyProgram::class, 'head_of_program_id');
    }

    /**
     * @return HasMany<CollageStudent, $this>
     */
    public function adviseeStudents(): HasMany
    {
        return $this->hasMany(CollageStudent::class, 'academic_advisor_id');
    }

    /**
     * @return HasMany<Thesis, $this>
     */
    public function advisedTheses(): HasMany
    {
        return $this->hasMany(Thesis::class, 'advisor_lecturer_id');
    }

    /**
     * @return HasMany<Thesis, $this>
     */
    public function examinedTheses(): HasMany
    {
        return $this->hasMany(Thesis::class, 'examiner_lecturer_id');
    }
}
