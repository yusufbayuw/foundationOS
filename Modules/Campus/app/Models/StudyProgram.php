<?php

namespace Modules\Campus\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Monitoring\Models\AuditLog;
use Modules\Monitoring\Models\FileUpload;
use Modules\Core\Models\Organization;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Modules\Core\Models\Concerns\BelongsToTenant;

class StudyProgram extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'organization_id',
        'faculty_id',
        'head_of_program_id',
        'code',
        'name',
        'degree_level',
        'accreditation',
        'description',
        'total_credits_required',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'total_credits_required' => 'integer',
            'is_active' => 'boolean',
        ];
    }
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class);
    }

    public function headOfProgram(): BelongsTo
    {
        return $this->belongsTo(Lecturer::class, 'head_of_program_id');
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    public function lecturers(): HasMany
    {
        return $this->hasMany(Lecturer::class);
    }

    public function students(): HasMany
    {
        return $this->hasMany(CollageStudent::class);
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
