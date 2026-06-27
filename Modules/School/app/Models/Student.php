<?php

namespace Modules\School\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\Models\AcademicYear;
use Modules\Core\Models\Concerns\BelongsToTenant;
use Modules\Core\Models\Organization;
use Modules\Core\Models\ParentStudent;
use Modules\Core\Models\User;
use Modules\Finance\Models\StudentInvoice;
use Modules\Monitoring\Models\AuditLog;
use Modules\Monitoring\Models\FileUpload;

class Student extends Model
{
    /** @use HasFactory<Factory<static>> */
    use BelongsToTenant, HasFactory, SoftDeletes;

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

    /**
     * @return BelongsTo<Organization, $this>
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * @return BelongsTo<AcademicYear, $this>
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<ClassStudent, $this>
     */
    public function classStudents(): HasMany
    {
        return $this->hasMany(ClassStudent::class);
    }

    /**
     * @return HasMany<Attendance, $this>
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * @return HasMany<StudentGrade, $this>
     */
    public function studentGrades(): HasMany
    {
        return $this->hasMany(StudentGrade::class);
    }

    /**
     * @return HasMany<Violation, $this>
     */
    public function violations(): HasMany
    {
        return $this->hasMany(Violation::class);
    }

    /**
     * @return HasMany<StudentAssessmentAnswer, $this>
     */
    public function studentAssessmentAnswers(): HasMany
    {
        return $this->hasMany(StudentAssessmentAnswer::class);
    }

    /**
     * @return HasMany<StudentAchievement, $this>
     */
    public function studentAchievements(): HasMany
    {
        return $this->hasMany(StudentAchievement::class);
    }

    /**
     * @return MorphMany<StudentInvoice, $this>
     */
    public function studentInvoices(): MorphMany
    {
        return $this->morphMany(StudentInvoice::class, 'invoiceable');
    }

    /**
     * @return MorphMany<AuditLog, $this>
     */
    public function auditLogs(): MorphMany
    {
        return $this->morphMany(AuditLog::class, 'auditable');
    }

    /**
     * @return MorphMany<FileUpload, $this>
     */
    public function fileUploads(): MorphMany
    {
        return $this->morphMany(FileUpload::class, 'fileable');
    }

    /**
     * @return HasOne<StudentRiskScore, $this>
     */
    public function riskScore(): HasOne
    {
        return $this->hasOne(StudentRiskScore::class);
    }

    /**
     * @return HasMany<ParentStudent, $this>
     */
    public function parentLinks(): HasMany
    {
        return $this->hasMany(ParentStudent::class);
    }
}
