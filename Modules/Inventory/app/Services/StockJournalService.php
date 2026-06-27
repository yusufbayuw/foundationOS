<?php

namespace Modules\Inventory\Services;

use App\Support\TypedValue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Modules\Core\Models\Organization;
use Modules\Finance\Models\ChartOfAccount;
use Modules\Finance\Models\JournalEntry;
use Modules\Finance\Models\JournalEntryLine;
use Modules\Inventory\Enums\StockMoveType;
use Modules\Inventory\Models\StockMove;
use RuntimeException;

class StockJournalService
{
    public function postForMove(StockMove $move): JournalEntry
    {
        if ($move->journal_entry_id) {
            return TypedValue::model($move->journalEntry);
        }

        return DB::transaction(function () use ($move): JournalEntry {
            $move->load(['stockItem', 'warehouse']);
            $stockItem = TypedValue::model($move->stockItem, 'Stock item must exist before posting stock journal.');
            $tenantId = TypedValue::int($move->tenant_id);
            $amount = (float) $move->total_cost;

            if ($amount <= 0) {
                throw new RuntimeException('Cannot post journal for zero-value stock move.');
            }

            $organizationId = $move->organization_id
                ?? Organization::withoutTenantScope()
                    ->where('tenant_id', $tenantId)
                    ->orderBy('id')
                    ->value('id');

            $inventoryCoa = $this->resolveCoa(
                $tenantId,
                $stockItem->inventory_coa_id,
                'asset',
                ['persediaan', 'inventory'],
                'Stock journal requires an inventory (persediaan) account.',
            );

            $offsetCoa = $move->move_type === StockMoveType::In
                ? $this->resolveCoa(
                    $tenantId,
                    null,
                    'liability',
                    ['gr', 'goods received', 'hutang'],
                    'Stock-in journal requires a GR/IR liability account.',
                )
                : $this->resolveCoa(
                    $tenantId,
                    $stockItem->cogs_coa_id,
                    'expense',
                    ['hpp', 'cogs', 'beban pokok'],
                    'Stock-out journal requires a COGS expense account.',
                );

            $entryNumber = 'JE-STK-'.$move->move_number;

            $journal = JournalEntry::query()->create([
                'tenant_id' => $move->tenant_id,
                'organization_id' => $organizationId,
                'entry_number' => $entryNumber,
                'date' => $move->moved_at?->toDateString() ?? now()->toDateString(),
                'description' => sprintf(
                    'Auto-journal stock %s %s',
                    $move->move_type->value,
                    $move->move_number,
                ),
                'total_debit' => $amount,
                'total_credit' => $amount,
                'is_balanced' => true,
                'is_posted' => false,
            ]);

            if ($move->move_type === StockMoveType::In) {
                JournalEntryLine::query()->create([
                    'tenant_id' => $move->tenant_id,
                    'journal_entry_id' => $journal->id,
                    'chart_of_account_id' => $inventoryCoa->id,
                    'description' => 'Persediaan — '.$stockItem->name,
                    'debit' => $amount,
                    'credit' => 0,
                ]);
                JournalEntryLine::query()->create([
                    'tenant_id' => $move->tenant_id,
                    'journal_entry_id' => $journal->id,
                    'chart_of_account_id' => $offsetCoa->id,
                    'description' => 'GR/IR — '.$stockItem->name,
                    'debit' => 0,
                    'credit' => $amount,
                ]);
            } else {
                JournalEntryLine::query()->create([
                    'tenant_id' => $move->tenant_id,
                    'journal_entry_id' => $journal->id,
                    'chart_of_account_id' => $offsetCoa->id,
                    'description' => 'HPP — '.$stockItem->name,
                    'debit' => $amount,
                    'credit' => 0,
                ]);
                JournalEntryLine::query()->create([
                    'tenant_id' => $move->tenant_id,
                    'journal_entry_id' => $journal->id,
                    'chart_of_account_id' => $inventoryCoa->id,
                    'description' => 'Persediaan — '.$stockItem->name,
                    'debit' => 0,
                    'credit' => $amount,
                ]);
            }

            $move->forceFill(['journal_entry_id' => $journal->id])->save();

            return $journal;
        });
    }

    /**
     * @param  array<int, string>  $keywords
     */
    private function resolveCoa(
        int $tenantId,
        ?int $preferredId,
        string $type,
        array $keywords,
        string $message,
    ): ChartOfAccount {
        if ($preferredId) {
            $preferred = ChartOfAccount::withoutTenantScope()
                ->where('tenant_id', $tenantId)
                ->whereKey($preferredId)
                ->where('is_active', true)
                ->first();

            if ($preferred) {
                return $preferred;
            }
        }

        $account = ChartOfAccount::withoutTenantScope()
            ->where('tenant_id', $tenantId)
            ->where('type', $type)
            ->where('is_active', true)
            ->where(function (Builder $query) use ($keywords): void {
                foreach ($keywords as $keyword) {
                    $query->orWhereRaw('LOWER(name) LIKE ?', ['%'.strtolower($keyword).'%']);
                }
            })
            ->first();

        if (! $account) {
            throw new RuntimeException($message);
        }

        return $account;
    }
}
