<?php

namespace Modules\Enrollment\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\Models\Concerns\BelongsToTenant;
use Modules\Core\Models\Department;
use Modules\Finance\Models\StudentInvoice;
use Modules\Monitoring\Models\AuditLog;
use Modules\Monitoring\Models\FileUpload;
use Modules\School\Models\Student;

class Applicant extends Model
{
    use BelongsToTenant, HasFactory, SoftDeletes;

    protected ?int $previousAdmissionPeriodId = null;

    public function synchronizeAssessmentSummary(): void
    {
        $examResults = $this->examResults()
            ->with('examSchedule')
            ->get();

        $scoredResults = $examResults->filter(fn (ExamResult $examResult) => $examResult->score !== null);

        $testResults = $scoredResults->filter(function (ExamResult $examResult) {
            return strtolower((string) $examResult->examSchedule?->type) === 'test';
        });

        $interviewResults = $scoredResults->filter(function (ExamResult $examResult) {
            return strtolower((string) $examResult->examSchedule?->type) === 'interview';
        });

        $evaluatedResults = $examResults->filter(fn (ExamResult $examResult) => $examResult->is_passed !== null);

        $aggregatedPassState = null;

        if ($evaluatedResults->isNotEmpty()) {
            $aggregatedPassState = $evaluatedResults->contains(fn (ExamResult $examResult) => $examResult->is_passed === false)
                ? false
                : true;
        }

        $this->forceFill([
            'test_score' => $testResults->isNotEmpty() ? round((float) $testResults->avg('score'), 2) : null,
            'interview_score' => $interviewResults->isNotEmpty() ? round((float) $interviewResults->avg('score'), 2) : null,
            'final_score' => $scoredResults->isNotEmpty() ? round((float) $scoredResults->avg('score'), 2) : null,
            'is_passed' => $aggregatedPassState,
        ])->saveQuietly();
    }

    protected static function booted(): void
    {
        static::updating(function (self $applicant): void {
            $applicant->previousAdmissionPeriodId = $applicant->getOriginal('admission_period_id');
        });

        static::saved(function (self $applicant): void {
            AdmissionPeriod::find($applicant->admission_period_id)?->synchronizeCounters();

            $originalAdmissionPeriodId = $applicant->previousAdmissionPeriodId ?? null;

            if ($originalAdmissionPeriodId && $originalAdmissionPeriodId !== $applicant->admission_period_id) {
                AdmissionPeriod::find($originalAdmissionPeriodId)?->synchronizeCounters();
            }
        });

        static::deleted(function (self $applicant): void {
            AdmissionPeriod::find($applicant->admission_period_id)?->synchronizeCounters();
        });
    }

    protected $fillable = [
        'tenant_id',
        'lead_id',
        'admission_period_id',
        'registration_number',
        'full_name',
        'birth_place',
        'birth_date',
        'gender',
        'religion',
        'address',
        'phone',
        'email',
        'parent_name',
        'parent_phone',
        'previous_school',
        'previous_school_address',
        'nisn',
        'ijazah_number',
        'average_score',
        'achievement_count',
        'achievement_details',
        'program_choice_1_id',
        'program_choice_2_id',
        'status',
        'test_score',
        'interview_score',
        'final_score',
        'ranking',
        'is_passed',
        'accepted_program_id',
        'enrollment_date',
        'converted_to_student_id',
        'photo',
        'documents',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'average_score' => 'decimal:2',
            'achievement_count' => 'integer',
            'achievement_details' => 'array',
            'test_score' => 'decimal:2',
            'interview_score' => 'decimal:2',
            'final_score' => 'decimal:2',
            'ranking' => 'integer',
            'is_passed' => 'boolean',
            'enrollment_date' => 'date',
            'documents' => 'array',
        ];
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function admissionPeriod(): BelongsTo
    {
        return $this->belongsTo(AdmissionPeriod::class);
    }

    public function firstProgramChoice(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'program_choice_1_id');
    }

    public function secondProgramChoice(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'program_choice_2_id');
    }

    public function acceptedProgram(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'accepted_program_id');
    }

    public function convertedStudent(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'converted_to_student_id');
    }

    public function examResults(): HasMany
    {
        return $this->hasMany(ExamResult::class);
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class);
    }

    public function registration(): HasOne
    {
        return $this->hasOne(Registration::class);
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
