<?php

namespace Modules\Core\Models\Concerns;

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

/**
 * Cross-module Eloquent relations kept for backward compatibility.
 *
 * @deprecated Prefer querying domain models from their owning modules.
 */
trait HasLegacyOrganizationDomainRelations
{
    public function admissionPeriods(): HasMany
    {
        return $this->hasMany(AdmissionPeriod::class);
    }

    public function curricula(): HasMany
    {
        return $this->hasMany(Curriculum::class);
    }

    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class);
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function teachers(): HasMany
    {
        return $this->hasMany(Teacher::class);
    }

    public function schoolClasses(): HasMany
    {
        return $this->hasMany(SchoolClass::class, 'organization_id');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    public function assessments(): HasMany
    {
        return $this->hasMany(Assessment::class);
    }

    public function achievementTypes(): HasMany
    {
        return $this->hasMany(AchievementType::class);
    }

    public function studentAchievements(): HasMany
    {
        return $this->hasMany(StudentAchievement::class);
    }

    public function violationTypes(): HasMany
    {
        return $this->hasMany(ViolationType::class);
    }

    public function chartOfAccounts(): HasMany
    {
        return $this->hasMany(ChartOfAccount::class);
    }

    public function tuitionTypes(): HasMany
    {
        return $this->hasMany(TuitionType::class);
    }

    public function journalEntries(): HasMany
    {
        return $this->hasMany(JournalEntry::class);
    }

    public function budgets(): HasMany
    {
        return $this->hasMany(Budget::class);
    }

    public function bookCategories(): HasMany
    {
        return $this->hasMany(BookCategory::class);
    }

    public function books(): HasMany
    {
        return $this->hasMany(Book::class);
    }

    public function bookReservations(): HasMany
    {
        return $this->hasMany(BookReservation::class);
    }

    public function libraryPolicies(): HasMany
    {
        return $this->hasMany(LibraryPolicy::class);
    }

    public function positions(): HasMany
    {
        return $this->hasMany(Position::class);
    }

    public function shifts(): HasMany
    {
        return $this->hasMany(Shift::class);
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    public function payrollComponents(): HasMany
    {
        return $this->hasMany(PayrollComponent::class);
    }

    public function kpiTemplates(): HasMany
    {
        return $this->hasMany(KpiTemplate::class);
    }

    public function kpiIndicators(): HasMany
    {
        return $this->hasMany(KpiIndicator::class);
    }

    public function procurementCategories(): HasMany
    {
        return $this->hasMany(ProcurementCategory::class);
    }

    public function faculties(): HasMany
    {
        return $this->hasMany(Faculty::class);
    }

    public function studyPrograms(): HasMany
    {
        return $this->hasMany(StudyProgram::class);
    }

    public function lecturers(): HasMany
    {
        return $this->hasMany(Lecturer::class);
    }

    public function collageStudents(): HasMany
    {
        return $this->hasMany(CollageStudent::class);
    }

    public function courseOfferings(): HasMany
    {
        return $this->hasMany(CourseOffering::class);
    }

    public function feederLogs(): HasMany
    {
        return $this->hasMany(FeederLog::class);
    }

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
