<?php

namespace Modules\Campus\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Core\Models\Organization;
use Modules\Core\Models\User;
use Modules\Core\Models\Concerns\BelongsToTenant;

class Lecturer extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

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
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function studyProgram(): BelongsTo
    {
        return $this->belongsTo(StudyProgram::class);
    }

    public function courseOfferings(): HasMany
    {
        return $this->hasMany(CourseOffering::class);
    }

    public function headedStudyPrograms(): HasMany
    {
        return $this->hasMany(StudyProgram::class, 'head_of_program_id');
    }

    public function adviseeStudents(): HasMany
    {
        return $this->hasMany(CollageStudent::class, 'academic_advisor_id');
    }

    public function advisedTheses(): HasMany
    {
        return $this->hasMany(Thesis::class, 'advisor_lecturer_id');
    }

    public function examinedTheses(): HasMany
    {
        return $this->hasMany(Thesis::class, 'examiner_lecturer_id');
    }
}
