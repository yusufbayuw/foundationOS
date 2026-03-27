<?php

namespace Modules\Finance\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Modules\Core\Models\Tenant;

class StudentInvoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'tuition_type_id',
        'invoiceable_id',
        'invoiceable_type',
        'invoice_number',
        'invoice_type',
        'issue_date',
        'due_date',
        'amount',
        'discount_amount',
        'discount_reason',
        'penalty_amount',
        'total_amount',
        'paid_amount',
        'remaining_amount',
        'status',
        'description',
        'notes',
        'is_sent',
        'sent_at',
        'sent_via',
    ];

    protected function casts(): array
    {
        return [
            'issue_date' => 'date',
            'due_date' => 'date',
            'amount' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'penalty_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'remaining_amount' => 'decimal:2',
            'is_sent' => 'boolean',
            'sent_at' => 'datetime',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function tuitionType(): BelongsTo
    {
        return $this->belongsTo(TuitionType::class);
    }

    public function invoiceable(): MorphTo
    {
        return $this->morphTo();
    }

    public function items(): HasMany
    {
        return $this->hasMany(StudentInvoiceItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function isLockedForMutation(): bool
    {
        return in_array((string) $this->status, ['paid', 'void', 'cancelled'], true);
    }
}
