<?php

namespace Modules\School\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Core\Models\AcademicPeriod;
use Modules\Core\Models\Department;
use Modules\Core\Models\Organization;
use Modules\Core\Models\Concerns\BelongsToTenant;

class SchoolClass extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $table = 'classes';

    protected $fillable = [
        'tenant_id',
        'organization_id',
        'academic_period_id',
        'department_id',
        'homeroom_teacher_id',
        'assistant_teacher_id',
        'name',
        'code',
        'grade_level',
        'capacity',
        'student_count',
        'is_active',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'capacity' => 'integer',
            'student_count' => 'integer',
            'is_active' => 'boolean',
        ];
    }
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function academicPeriod(): BelongsTo
    {
        return $this->belongsTo(AcademicPeriod::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function homeroomTeacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'homeroom_teacher_id');
    }

    public function assistantTeacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'assistant_teacher_id');
    }

    public function classStudents(): HasMany
    {
        return $this->hasMany(ClassStudent::class, 'class_id');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class, 'class_id');
    }

    public function assessments(): HasMany
    {
        return $this->hasMany(Assessment::class, 'class_id');
    }
}
