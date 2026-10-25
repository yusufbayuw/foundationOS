<?php

namespace Modules\Finance\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\Models\Concerns\BelongsToTenant;
use RuntimeException;

class JournalEntryLine extends Model
{
    use BelongsToTenant, HasFactory, SoftDeletes;

    protected static function booted(): void
    {
        static::creating(function (self $line): void {
            $line->assertLedgerWritable();
        });

        static::updating(function (self $line): void {
            // A line may not be moved away from a finalized parent either.
            $line->assertLedgerWritable((int) $line->getRawOriginal('journal_entry_id'));
            $line->assertLedgerWritable();
        });

        static::deleting(function (self $line): void {
            $line->assertLedgerWritable();
        });
    }

    private function assertLedgerWritable(?int $originalJournalId = null): void
    {
        $journalId = $originalJournalId ?? (int) $this->getAttribute('journal_entry_id');

        $journal = JournalEntry::withoutTenantScope()
            ->where('tenant_id', $this->getAttribute('tenant_id'))
            ->find($journalId);

        if (! $journal || $journal->isLockedForMutation()) {
            throw new RuntimeException('Journal lines cannot be changed after posting or reversal.');
        }

        $debitCents = (int) round((float) $this->getAttribute('debit') * 100);
        $creditCents = (int) round((float) $this->getAttribute('credit') * 100);

        if ($debitCents < 0 || $creditCents < 0 || ($debitCents > 0 && $creditCents > 0)) {
            throw new RuntimeException('Journal line debit and credit must be non-negative and mutually exclusive.');
        }

        if ($this->getAttribute('chart_of_account_id')) {
            $account = ChartOfAccount::withoutTenantScope()
                ->where('tenant_id', $this->getAttribute('tenant_id'))
                ->find($this->getAttribute('chart_of_account_id'));

            if (! $account || (int) $account->organization_id !== (int) $journal->organization_id) {
                throw new RuntimeException('Journal line account must belong to the journal tenant and organization.');
            }
        }
    }

    protected $fillable = [
        'tenant_id',
        'journal_entry_id',
        'chart_of_account_id',
        'description',
        'debit',
        'credit',
    ];

    protected function casts(): array
    {
        return [
            'debit' => 'decimal:2',
            'credit' => 'decimal:2',
        ];
    }

    public function journalEntry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class);
    }

    public function chartOfAccount(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class);
    }
}
