<?php

namespace Modules\Procurement\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\Models\Concerns\BelongsToTenant;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;
use Modules\Workflow\Contracts\ProvidesWorkflowContext;
use Modules\Workflow\Contracts\StartsWorkflow;
use Modules\Workflow\Models\WorkflowInstance;

class PurchaseRequisition extends Model implements ProvidesWorkflowContext, StartsWorkflow
{
    use BelongsToTenant, HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'user_id',
        'requested_by',
        'approved_by',
        'request_number',
        'request_date',
        'required_date',
        'priority',
        'justification',
        'total_items',
        'total_estimated_amount',
        'status',
        'ready_for_sourcing',
        'approved_at',
        'rejection_reason',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'request_date' => 'date',
            'required_date' => 'date',
            'total_items' => 'integer',
            'total_estimated_amount' => 'decimal:2',
            'ready_for_sourcing' => 'boolean',
            'approved_at' => 'datetime',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseRequisitionItem::class);
    }

    public function rfqs(): HasMany
    {
        return $this->hasMany(RequestForQuotation::class);
    }

    public function workflowInstances(): MorphMany
    {
        return $this->morphMany(WorkflowInstance::class, 'subject', 'subject_type', 'subject_id');
    }

    public function workflowCode(): string
    {
        return 'purchase-requisition-approval';
    }

    public function workflowContext(): array
    {
        return [
            'tenant_id' => $this->tenant_id,
            'requested_by' => $this->requested_by,
            'request_number' => $this->request_number,
            'priority' => $this->priority,
            'status' => $this->status,
            'ready_for_sourcing' => (bool) $this->ready_for_sourcing,
            'total_items' => $this->total_items,
            'total_estimated_amount' => (float) $this->total_estimated_amount,
            'justification' => $this->justification,
            'notes' => $this->notes,
        ];
    }

    public function workflowSubjectLabel(): string
    {
        return (string) ($this->request_number ?: 'Purchase Requisition #'.$this->getKey());
    }

    public function workflowSubjectType(): string
    {
        return self::class;
    }

    public function isLockedForMutation(): bool
    {
        return in_array((string) $this->status, ['submitted', 'in_review', 'approved', 'rejected', 'cancelled'], true);
    }

    public function isPrintable(): bool
    {
        return (string) $this->status === 'approved';
    }
}
