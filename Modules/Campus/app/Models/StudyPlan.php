<?php

namespace Modules\Campus\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Core\Models\AcademicPeriod;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;

class StudyPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'collage_student_id',
        'academic_period_id',
        'approved_by',
        'plan_number',
        'total_credits',
        'status',
        'submitted_at',
        'approved_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'total_credits' => 'integer',
            'submitted_at' => 'datetime',
            'approved_at' => 'datetime',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function collageStudent(): BelongsTo
    {
        return $this->belongsTo(CollageStudent::class);
    }

    public function academicPeriod(): BelongsTo
    {
        return $this->belongsTo(AcademicPeriod::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(StudyPlanItem::class);
    }
}
