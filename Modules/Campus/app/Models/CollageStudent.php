<?php

namespace Modules\Campus\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\Models\Concerns\BelongsToTenant;
use Modules\Core\Models\Organization;
use Modules\Core\Models\User;
use Modules\Finance\Models\StudentInvoice;
use Modules\Monitoring\Models\AuditLog;
use Modules\Monitoring\Models\FileUpload;

class CollageStudent extends Model
{
    /** @use HasFactory<Factory<static>> */
    use BelongsToTenant, HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'organization_id',
        'user_id',
        'study_program_id',
        'academic_advisor_id',
        'student_number',
        'national_student_number',
        'full_name',
        'entry_year',
        'entry_semester',
        'admission_type',
        'current_semester',
        'status',
        'email',
        'phone',
        'graduation_date',
    ];

    protected function casts(): array
    {
        return [
            'entry_year' => 'integer',
            'entry_semester' => 'integer',
            'current_semester' => 'integer',
            'graduation_date' => 'date',
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
     * @return BelongsTo<Lecturer, $this>
     */
    public function academicAdvisor(): BelongsTo
    {
        return $this->belongsTo(Lecturer::class, 'academic_advisor_id');
    }

    /**
     * @return HasMany<StudyPlan, $this>
     */
    public function studyPlans(): HasMany
    {
        return $this->hasMany(StudyPlan::class);
    }

    /**
     * @return HasMany<Thesis, $this>
     */
    public function theses(): HasMany
    {
        return $this->hasMany(Thesis::class);
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

    public function isPrintable(): bool
    {
        return in_array((string) $this->status, ['active', 'graduated'], true);
    }
}
