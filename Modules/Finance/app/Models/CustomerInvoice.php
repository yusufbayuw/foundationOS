<?php

namespace Modules\Finance\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\Models\Concerns\BelongsToTenant;
use Modules\Core\Models\Organization;
use Modules\Monitoring\Models\Concerns\HasAuditTrail;

class CustomerInvoice extends Model
{
    /** @use HasFactory<Factory<static>> */
    use BelongsToTenant, HasAuditTrail, HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'organization_id',
        'invoice_number',
        'customer_name',
        'customer_email',
        'customer_phone',
        'customer_address',
        'invoice_type',
        'issue_date',
        'due_date',
        'description',
        'subtotal',
        'discount_amount',
        'discount_reason',
        'tax_amount',
        'total_amount',
        'paid_amount',
        'remaining_amount',
        'status',
        'notes',
        'is_sent',
        'sent_at',
        'journal_entry_id',
    ];

    protected function casts(): array
    {
        return [
            'issue_date' => 'date',
            'due_date' => 'date',
            'subtotal' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'remaining_amount' => 'decimal:2',
            'is_sent' => 'boolean',
            'sent_at' => 'datetime',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function invoiceTypeOptions(): array
    {
        return [
            'general' => 'General',
            'service' => 'Service',
            'product' => 'Product',
            'consulting' => 'Consulting',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function statusOptions(): array
    {
        return [
            'draft' => 'Draft',
            'issued' => 'Issued',
            'partial' => 'Partial',
            'paid' => 'Paid',
            'void' => 'Void',
            'cancelled' => 'Cancelled',
        ];
    }

    public function isOverdue(): bool
    {
        return ! in_array($this->status, ['paid', 'void', 'cancelled'])
            && $this->due_date->isPast();
    }

    public function isLockedForMutation(): bool
    {
        return in_array((string) $this->status, ['paid', 'void', 'cancelled'], true);
    }

    public function isPrintable(): bool
    {
        return ! in_array((string) $this->status, ['draft', 'void', 'cancelled'], true);
    }

    public function recalculate(): void
    {
        $this->load('items');
        $this->subtotal = $this->items->sum('line_total');
        $this->total_amount = $this->subtotal - $this->discount_amount + $this->tax_amount;
        $this->remaining_amount = $this->total_amount - $this->paid_amount;
        $this->saveQuietly();
    }

    /**
     * @return BelongsTo<Organization, $this>
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * @return HasMany<CustomerInvoiceItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(CustomerInvoiceItem::class);
    }

    /**
     * @return BelongsTo<JournalEntry, $this>
     */
    public function journalEntry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class);
    }
}
