<?php

namespace Modules\Finance\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\Models\Concerns\BelongsToTenant;
use Modules\Core\Models\User;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Payment extends Model
{
    use BelongsToTenant, HasFactory, LogsActivity, SoftDeletes;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['payment_number', 'amount', 'payment_method', 'payment_date', 'verified_by', 'status'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

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
