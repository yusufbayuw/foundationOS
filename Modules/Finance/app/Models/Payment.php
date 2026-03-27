<?php

namespace Modules\Finance\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'student_invoice_id',
        'chart_of_account_id',
        'verified_by',
        'payment_number',
        'payment_date',
        'amount',
        'payment_method',
        'payment_channel',
        'reference_number',
        'account_number',
        'account_holder',
        'bank_name',
        'proof_file',
        'verified_at',
        'verification_notes',
        'status',
        'is_reconciled',
        'reconciliation_date',
    ];

    protected function casts(): array
    {
        return [
            'payment_date' => 'date',
            'amount' => 'decimal:2',
            'verified_at' => 'datetime',
            'is_reconciled' => 'boolean',
            'reconciliation_date' => 'date',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function studentInvoice(): BelongsTo
    {
        return $this->belongsTo(StudentInvoice::class);
    }

    public function chartOfAccount(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class);
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function isLockedForMutation(): bool
    {
        return in_array((string) $this->status, ['verified', 'rejected', 'reversed'], true);
    }
}
