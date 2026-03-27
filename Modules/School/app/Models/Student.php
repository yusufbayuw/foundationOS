<?php

namespace Modules\School\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Modules\Finance\Models\StudentInvoice;
use Modules\Monitoring\Models\AuditLog;
use Modules\Monitoring\Models\FileUpload;
use Modules\Core\Models\AcademicYear;
use Modules\Core\Models\Organization;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;

class Student extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'organization_id',
        'academic_year_id',
        'user_id',
        'nis',
        'nisn',
        'entry_date',
        'entry_type',
        'previous_school',
        'previous_school_npsn',
        'status',
        'graduation_date',
        'ijazah_number',
        'skhun_number',
        'track',
        'extracurricular_activities',
        'achievements',
        'health_notes',
        'special_needs',
        'scholarship_status',
        'family_card_number',
        'father_name',
        'father_nik',
        'father_education',
        'father_job',
        'father_phone',
        'mother_name',
        'mother_nik',
        'mother_education',
        'mother_job',
        'mother_phone',
        'guardian_name',
        'guardian_relation',
        'guardian_phone',
        'guardian_address',
        'residence_type',
        'transport_type',
        'travel_time_minutes',
        'distance_km',
    ];

    protected function casts(): array
    {
        return [
            'entry_date' => 'date',
            'graduation_date' => 'date',
            'extracurricular_activities' => 'array',
            'achievements' => 'array',
            'health_notes' => 'array',
            'special_needs' => 'array',
            'travel_time_minutes' => 'integer',
            'distance_km' => 'decimal:2',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function classStudents(): HasMany
    {
        return $this->hasMany(ClassStudent::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function studentGrades(): HasMany
    {
        return $this->hasMany(StudentGrade::class);
    }

    public function violations(): HasMany
    {
        return $this->hasMany(Violation::class);
    }

    public function studentAssessmentAnswers(): HasMany
    {
        return $this->hasMany(StudentAssessmentAnswer::class);
    }

    public function studentAchievements(): HasMany
    {
        return $this->hasMany(StudentAchievement::class);
    }

    public function studentInvoices(): MorphMany
    {
        return $this->morphMany(StudentInvoice::class, 'invoiceable');
    }

    public function auditLogs(): MorphMany
    {
        return $this->morphMany(AuditLog::class, 'auditable');
    }

    public function fileUploads(): MorphMany
    {
        return $this->morphMany(FileUpload::class, 'fileable');
    }
}
