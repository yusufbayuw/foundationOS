<?php

namespace Modules\Employee\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Core\Models\User;
use Modules\Core\Models\Concerns\BelongsToTenant;
use Modules\Employee\Enums\KpiScoreStatus;

class KpiScore extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'employee_id',
        'kpi_template_id',
        'evaluator_id',
        'period_month',
        'period_year',
        'scores',
        'total_score',
        'grade',
        'qualitative_assessment',
        'employee_self_assessment',
        'development_plan',
        'status',
        'submitted_at',
        'evaluated_at',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'scores' => 'array',
            'total_score' => 'decimal:2',
            'status' => KpiScoreStatus::class,
            'submitted_at' => 'datetime',
            'evaluated_at' => 'datetime',
            'approved_at' => 'datetime',
        ];
    }

    public function isLockedForMutation(): bool
    {
        return $this->status?->isLockedForMutation() ?? false;
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(KpiTemplate::class, 'kpi_template_id');
    }

    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }
}
