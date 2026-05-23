<?php

namespace Modules\Finance\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\Models\Concerns\BelongsToTenant;
use Modules\Core\Models\Organization;
use Modules\Core\Models\User;
use Modules\Workflow\Contracts\ProvidesWorkflowContext;
use Modules\Workflow\Contracts\StartsWorkflow;
use Modules\Workflow\Models\WorkflowInstance;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Budget extends Model implements ProvidesWorkflowContext, StartsWorkflow
{
    use BelongsToTenant, HasFactory, LogsActivity, SoftDeletes;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['budget_number', 'name', 'total_amount', 'status', 'approved_by', 'approved_at'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    protected $fillable = [
        'tenant_id',
        'organization_id',
        'chart_of_account_id',
        'approved_by',
        'fiscal_year',
        'name',
        'code',
        'allocated_amount',
        'used_amount',
        'remaining_amount',
        'description',
        'status',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'allocated_amount' => 'decimal:2',
            'used_amount' => 'decimal:2',
            'remaining_amount' => 'decimal:2',
            'approved_at' => 'datetime',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function chartOfAccount(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function workflowInstances(): MorphMany
    {
        return $this->morphMany(WorkflowInstance::class, 'subject', 'subject_type', 'subject_id');
    }

    public function isLockedForMutation(): bool
    {
        return in_array((string) $this->status, ['submitted', 'in_review', 'approved', 'closed', 'cancelled', 'rejected'], true);
    }

    public function workflowCode(): string
    {
        return 'budget-approval';
    }

    public function workflowContext(): array
    {
        return [
            'tenant_id' => $this->tenant_id,
            'organization_id' => $this->organization_id,
            'budget_code' => $this->code,
            'budget_name' => $this->name,
            'fiscal_year' => $this->fiscal_year,
            'allocated_amount' => (float) $this->allocated_amount,
            'used_amount' => (float) $this->used_amount,
            'remaining_amount' => (float) $this->remaining_amount,
            'status' => $this->status,
            'description' => $this->description,
        ];
    }

    public function workflowSubjectLabel(): string
    {
        return (string) ($this->code ?: 'Budget #'.$this->getKey());
    }

    public function workflowSubjectType(): string
    {
        return self::class;
    }
}
