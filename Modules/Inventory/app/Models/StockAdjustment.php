<?php

namespace Modules\Inventory\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\Models\Concerns\BelongsToTenant;
use Modules\Core\Models\Organization;
use Modules\Finance\Models\JournalEntry;
use Modules\Inventory\Enums\StockAdjustmentReason;
use Modules\Monitoring\Models\Concerns\HasAuditTrail;
use Modules\Workflow\Contracts\ProvidesWorkflowContext;
use Modules\Workflow\Contracts\StartsWorkflow;
use Modules\Workflow\Models\WorkflowInstance;

class StockAdjustment extends Model implements ProvidesWorkflowContext, StartsWorkflow
{
    use BelongsToTenant, HasAuditTrail, HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'organization_id',
        'warehouse_id',
        'adjustment_number',
        'reason',
        'status',
        'total_value_impact',
        'journal_entry_id',
        'approved_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'reason' => StockAdjustmentReason::class,
            'total_value_impact' => 'decimal:2',
            'approved_at' => 'datetime',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function journalEntry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class);
    }

    public function lines(): HasMany
    {
        return $this->hasMany(StockAdjustmentLine::class);
    }

    public function workflowInstances(): MorphMany
    {
        return $this->morphMany(WorkflowInstance::class, 'subject', 'subject_type', 'subject_id');
    }

    public function isLockedForMutation(): bool
    {
        return in_array((string) $this->status, ['submitted', 'in_review', 'approved', 'committed', 'cancelled', 'rejected'], true);
    }

    public function workflowCode(): string
    {
        return 'stock-adjustment-approval';
    }

    public function workflowContext(): array
    {
        return [
            'tenant_id' => $this->tenant_id,
            'organization_id' => $this->organization_id,
            'adjustment_number' => $this->adjustment_number,
            'reason' => $this->reason?->value,
            'total_value_impact' => (float) $this->total_value_impact,
            'status' => $this->status,
        ];
    }

    public function workflowSubjectLabel(): string
    {
        return (string) ($this->adjustment_number ?: 'Adjustment #'.$this->getKey());
    }

    public function workflowSubjectType(): string
    {
        return self::class;
    }
}
