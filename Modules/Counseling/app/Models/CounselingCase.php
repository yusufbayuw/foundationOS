<?php

namespace Modules\Counseling\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\Models\Concerns\BelongsToTenant;
use Modules\Core\Models\Organization;
use Modules\Monitoring\Models\Concerns\HasAuditTrail;
use Modules\School\Models\Student;

class CounselingCase extends Model
{
    /** @use HasFactory<Factory<static>> */
    use BelongsToTenant, HasAuditTrail, HasFactory, SoftDeletes;

    protected $table = 'counseling_cases';

    protected $fillable = [
        'tenant_id',
        'organization_id',
        'student_id',
        'counselor_id',
        'code',
        'name',
        'status',
        'risk_level',
        'case_target_type',
        'case_target_id',
        'description',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'meta' => 'array',
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
     * @return BelongsTo<Student, $this>
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * @return BelongsTo<Counselor, $this>
     */
    public function counselor(): BelongsTo
    {
        return $this->belongsTo(Counselor::class);
    }

    /**
     * @return MorphTo<Model, $this>
     */
    public function caseTarget(): MorphTo
    {
        return $this->morphTo();
    }
}
