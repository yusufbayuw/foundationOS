<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Modules\Campus\Models\CollageStudent;
use Modules\Campus\Models\CourseOffering;
use Modules\Campus\Models\Faculty;
use Modules\Campus\Models\FeederLog;
use Modules\Campus\Models\Lecturer;
use Modules\Campus\Models\StudyProgram;
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
use Modules\Core\Models\Concerns\BelongsToTenant;

class Organization extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
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
    public function principalUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'principal_user_id');
    }

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class);
    }

    public function organizationSettings(): HasMany
    {
        return $this->hasMany(OrganizationSetting::class);
    }

    public function userTenantRoles(): HasMany
    {
        return $this->hasMany(UserTenantRole::class);
    }

    public function academicYears(): HasMany
    {
        return $this->hasMany(AcademicYear::class);
    }

    public function academicPeriods(): HasMany
    {
        return $this->hasMany(AcademicPeriod::class);
    }

    public function departments(): HasMany
    {
        return $this->hasMany(Department::class);
    }

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

    public function admissionPeriods(): HasMany { return $this->hasMany(AdmissionPeriod::class); }
    public function curricula(): HasMany { return $this->hasMany(Curriculum::class); }
    public function subjects(): HasMany { return $this->hasMany(Subject::class); }
    public function students(): HasMany { return $this->hasMany(Student::class); }
    public function teachers(): HasMany { return $this->hasMany(Teacher::class); }
    public function schoolClasses(): HasMany { return $this->hasMany(SchoolClass::class, 'organization_id'); }
    public function schedules(): HasMany { return $this->hasMany(Schedule::class); }
    public function assessments(): HasMany { return $this->hasMany(Assessment::class); }
    public function achievementTypes(): HasMany { return $this->hasMany(AchievementType::class); }
    public function studentAchievements(): HasMany { return $this->hasMany(StudentAchievement::class); }
    public function violationTypes(): HasMany { return $this->hasMany(ViolationType::class); }
    public function chartOfAccounts(): HasMany { return $this->hasMany(ChartOfAccount::class); }
    public function tuitionTypes(): HasMany { return $this->hasMany(TuitionType::class); }
    public function journalEntries(): HasMany { return $this->hasMany(JournalEntry::class); }
    public function budgets(): HasMany { return $this->hasMany(Budget::class); }
    public function bookCategories(): HasMany { return $this->hasMany(BookCategory::class); }
    public function books(): HasMany { return $this->hasMany(Book::class); }
    public function bookReservations(): HasMany { return $this->hasMany(BookReservation::class); }
    public function libraryPolicies(): HasMany { return $this->hasMany(LibraryPolicy::class); }
    public function positions(): HasMany { return $this->hasMany(Position::class); }
    public function shifts(): HasMany { return $this->hasMany(Shift::class); }
    public function employees(): HasMany { return $this->hasMany(Employee::class); }
    public function payrollComponents(): HasMany { return $this->hasMany(PayrollComponent::class); }
    public function kpiTemplates(): HasMany { return $this->hasMany(KpiTemplate::class); }
    public function kpiIndicators(): HasMany { return $this->hasMany(KpiIndicator::class); }
    public function procurementCategories(): HasMany { return $this->hasMany(ProcurementCategory::class); }
    public function faculties(): HasMany { return $this->hasMany(Faculty::class); }
    public function studyPrograms(): HasMany { return $this->hasMany(StudyProgram::class); }
    public function lecturers(): HasMany { return $this->hasMany(Lecturer::class); }
    public function collageStudents(): HasMany { return $this->hasMany(CollageStudent::class); }
    public function courseOfferings(): HasMany { return $this->hasMany(CourseOffering::class); }
    public function feederLogs(): HasMany { return $this->hasMany(FeederLog::class); }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    public function fileUploads(): HasMany
    {
        return $this->hasMany(FileUpload::class);
    }

    public function auditableLogs(): MorphMany
    {
        return $this->morphMany(AuditLog::class, 'auditable');
    }

    public function attachedFiles(): MorphMany
    {
        return $this->morphMany(FileUpload::class, 'fileable');
    }
}
