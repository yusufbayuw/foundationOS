<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Campus\Models\CollageStudent;
use Modules\Campus\Models\CourseOffering;
use Modules\Campus\Models\Faculty;
use Modules\Campus\Models\FeederLog;
use Modules\Campus\Models\Lecturer;
use Modules\Campus\Models\StudyProgram;
use Modules\Core\Models\Concerns\BelongsToTenant;
use Modules\Employee\Models\Employee;
use Modules\Employee\Models\KpiIndicator;
use Modules\Employee\Models\KpiTemplate;
use Modules\Employee\Models\PayrollComponent;
use Modules\Employee\Models\Position;
use Modules\Employee\Models\Shift;
use Modules\Enrollment\Models\AdmissionPeriod;
use Modules\Finance\Models\Budget;
use Modules\Finance\Models\ChartOfAccount;
use Modules\Finance\Models\JournalEntry;
use Modules\Finance\Models\TuitionType;
use Modules\Global\Models\City;
use Modules\Global\Models\District;
use Modules\Global\Models\Province;
use Modules\Global\Models\Village;
use Modules\Library\Models\Book;
use Modules\Library\Models\BookCategory;
use Modules\Library\Models\BookReservation;
use Modules\Library\Models\LibraryPolicy;
use Modules\Monitoring\Models\AuditLog;
use Modules\Monitoring\Models\FileUpload;
use Modules\Procurement\Models\ProcurementCategory;
use Modules\School\Models\AchievementType;
use Modules\School\Models\Assessment;
use Modules\School\Models\Curriculum;
use Modules\School\Models\Schedule;
use Modules\School\Models\SchoolClass;
use Modules\School\Models\Student;
use Modules\School\Models\StudentAchievement;
use Modules\School\Models\Subject;
use Modules\School\Models\Teacher;
use Modules\School\Models\ViolationType;

class Organization extends Model
{
    /** @use HasFactory<Factory<static>> */
    use BelongsToTenant, HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'parent_organization_id',
        'organization_type',
        'code',
        'name',
        'short_name',
        'type',
        'level',
        'npsn',
        'nss',
        'accreditation_status',
        'npwp',
        'phone',
        'email',
        'website',
        'address',
        'province_id',
        'city_id',
        'district_id',
        'village_id',
        'postal_code',
        'latitude',
        'longitude',
        'established_date',
        'principal_user_id',
        'logo',
        'stamp',
        'signature',
        'letterhead',
        'is_main',
        'is_active',
        'settings',
    ];

    protected function casts(): array
    {
        return [
            'settings' => 'array',
            'is_main' => 'boolean',
            'is_active' => 'boolean',
            'established_date' => 'date',
        ];
    }

    /**
     * @return BelongsTo<Organization, $this>
     */
    public function parentOrganization(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_organization_id');
    }

    /**
     * @return HasMany<Organization, $this>
     */
    public function childOrganizations(): HasMany
    {
        return $this->hasMany(self::class, 'parent_organization_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function principalUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'principal_user_id');
    }

    /**
     * @return BelongsTo<Province, $this>
     */
    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }

    /**
     * @return BelongsTo<City, $this>
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    /**
     * @return BelongsTo<District, $this>
     */
    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    /**
     * @return BelongsTo<Village, $this>
     */
    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class);
    }

    /**
     * @return HasMany<OrganizationSetting, $this>
     */
    public function organizationSettings(): HasMany
    {
        return $this->hasMany(OrganizationSetting::class);
    }

    /**
     * @return HasMany<UserTenantRole, $this>
     */
    public function userTenantRoles(): HasMany
    {
        return $this->hasMany(UserTenantRole::class);
    }

    /**
     * @return HasMany<AcademicYear, $this>
     */
    public function academicYears(): HasMany
    {
        return $this->hasMany(AcademicYear::class);
    }

    /**
     * @return HasMany<AcademicPeriod, $this>
     */
    public function academicPeriods(): HasMany
    {
        return $this->hasMany(AcademicPeriod::class);
    }

    /**
     * @return HasMany<Department, $this>
     */
    public function departments(): HasMany
    {
        return $this->hasMany(Department::class);
    }

    /**
     * @return BelongsToMany<User, $this>
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_tenant_roles')
            ->withPivot([
                'tenant_id',
                'tenant_role_id',
                'assigned_by',
                'assigned_at',
                'expires_at',
                'is_primary',
            ])
            ->withTimestamps();
    }

    /**
     * @return HasMany<AdmissionPeriod, $this>
     */
    public function admissionPeriods(): HasMany
    {
        return $this->hasMany(AdmissionPeriod::class);
    }

    /**
     * @return HasMany<Curriculum, $this>
     */
    public function curricula(): HasMany
    {
        return $this->hasMany(Curriculum::class);
    }

    /**
     * @return HasMany<Subject, $this>
     */
    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class);
    }

    /**
     * @return HasMany<Student, $this>
     */
    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    /**
     * @return HasMany<Teacher, $this>
     */
    public function teachers(): HasMany
    {
        return $this->hasMany(Teacher::class);
    }

    /**
     * @return HasMany<SchoolClass, $this>
     */
    public function schoolClasses(): HasMany
    {
        return $this->hasMany(SchoolClass::class, 'organization_id');
    }

    /**
     * @return HasMany<Schedule, $this>
     */
    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    /**
     * @return HasMany<Assessment, $this>
     */
    public function assessments(): HasMany
    {
        return $this->hasMany(Assessment::class);
    }

    /**
     * @return HasMany<AchievementType, $this>
     */
    public function achievementTypes(): HasMany
    {
        return $this->hasMany(AchievementType::class);
    }

    /**
     * @return HasMany<StudentAchievement, $this>
     */
    public function studentAchievements(): HasMany
    {
        return $this->hasMany(StudentAchievement::class);
    }

    /**
     * @return HasMany<ViolationType, $this>
     */
    public function violationTypes(): HasMany
    {
        return $this->hasMany(ViolationType::class);
    }

    /**
     * @return HasMany<ChartOfAccount, $this>
     */
    public function chartOfAccounts(): HasMany
    {
        return $this->hasMany(ChartOfAccount::class);
    }

    /**
     * @return HasMany<TuitionType, $this>
     */
    public function tuitionTypes(): HasMany
    {
        return $this->hasMany(TuitionType::class);
    }

    /**
     * @return HasMany<JournalEntry, $this>
     */
    public function journalEntries(): HasMany
    {
        return $this->hasMany(JournalEntry::class);
    }

    /**
     * @return HasMany<Budget, $this>
     */
    public function budgets(): HasMany
    {
        return $this->hasMany(Budget::class);
    }

    /**
     * @return HasMany<BookCategory, $this>
     */
    public function bookCategories(): HasMany
    {
        return $this->hasMany(BookCategory::class);
    }

    /**
     * @return HasMany<Book, $this>
     */
    public function books(): HasMany
    {
        return $this->hasMany(Book::class);
    }

    /**
     * @return HasMany<BookReservation, $this>
     */
    public function bookReservations(): HasMany
    {
        return $this->hasMany(BookReservation::class);
    }

    /**
     * @return HasMany<LibraryPolicy, $this>
     */
    public function libraryPolicies(): HasMany
    {
        return $this->hasMany(LibraryPolicy::class);
    }

    /**
     * @return HasMany<Position, $this>
     */
    public function positions(): HasMany
    {
        return $this->hasMany(Position::class);
    }

    /**
     * @return HasMany<Shift, $this>
     */
    public function shifts(): HasMany
    {
        return $this->hasMany(Shift::class);
    }

    /**
     * @return HasMany<Employee, $this>
     */
    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    /**
     * @return HasMany<PayrollComponent, $this>
     */
    public function payrollComponents(): HasMany
    {
        return $this->hasMany(PayrollComponent::class);
    }

    /**
     * @return HasMany<KpiTemplate, $this>
     */
    public function kpiTemplates(): HasMany
    {
        return $this->hasMany(KpiTemplate::class);
    }

    /**
     * @return HasMany<KpiIndicator, $this>
     */
    public function kpiIndicators(): HasMany
    {
        return $this->hasMany(KpiIndicator::class);
    }

    /**
     * @return HasMany<ProcurementCategory, $this>
     */
    public function procurementCategories(): HasMany
    {
        return $this->hasMany(ProcurementCategory::class);
    }

    /**
     * @return HasMany<Faculty, $this>
     */
    public function faculties(): HasMany
    {
        return $this->hasMany(Faculty::class);
    }

    /**
     * @return HasMany<StudyProgram, $this>
     */
    public function studyPrograms(): HasMany
    {
        return $this->hasMany(StudyProgram::class);
    }

    /**
     * @return HasMany<Lecturer, $this>
     */
    public function lecturers(): HasMany
    {
        return $this->hasMany(Lecturer::class);
    }

    /**
     * @return HasMany<CollageStudent, $this>
     */
    public function collageStudents(): HasMany
    {
        return $this->hasMany(CollageStudent::class);
    }

    /**
     * @return HasMany<CourseOffering, $this>
     */
    public function courseOfferings(): HasMany
    {
        return $this->hasMany(CourseOffering::class);
    }

    /**
     * @return HasMany<FeederLog, $this>
     */
    public function feederLogs(): HasMany
    {
        return $this->hasMany(FeederLog::class);
    }

    /**
     * @return HasMany<AuditLog, $this>
     */
    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    /**
     * @return HasMany<FileUpload, $this>
     */
    public function fileUploads(): HasMany
    {
        return $this->hasMany(FileUpload::class);
    }

    /**
     * @return MorphMany<AuditLog, $this>
     */
    public function auditableLogs(): MorphMany
    {
        return $this->morphMany(AuditLog::class, 'auditable');
    }

    /**
     * @return MorphMany<FileUpload, $this>
     */
    public function attachedFiles(): MorphMany
    {
        return $this->morphMany(FileUpload::class, 'fileable');
    }
}
