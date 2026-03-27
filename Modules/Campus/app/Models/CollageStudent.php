<?php

namespace Modules\Campus\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Modules\Core\Models\Organization;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;
use Modules\Finance\Models\StudentInvoice;
use Modules\Monitoring\Models\AuditLog;
use Modules\Monitoring\Models\FileUpload;

class CollageStudent extends Model
{
    use HasFactory, SoftDeletes;

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

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
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

    public function academicAdvisor(): BelongsTo
    {
        return $this->belongsTo(Lecturer::class, 'academic_advisor_id');
    }

    public function studyPlans(): HasMany
    {
        return $this->hasMany(StudyPlan::class);
    }

    public function theses(): HasMany
    {
        return $this->hasMany(Thesis::class);
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
