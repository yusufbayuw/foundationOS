<?php

namespace Modules\Finance\Services;

use App\Support\TypedValue;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Modules\Finance\Models\ChartOfAccount;
use Modules\Finance\Models\JournalEntry;
use Modules\Finance\Models\JournalEntryLine;

/**
 * Generates P&L, Balance Sheet, and Cash Flow reports by aggregating
 * posted JournalEntryLine records grouped by ChartOfAccount.type.
 *
 * Account type conventions (stored as lowercase strings in the DB):
 *   asset, liability, equity, revenue, expense
 *
 * @phpstan-type AccountSummary array{id: int|null, code: string, name: string, balance: float}
 */
class FinancialReportService
{
    // ─────────────────────────────────────────────────────
    // Profit & Loss
    // ─────────────────────────────────────────────────────

    /** @return array<string, mixed> */
    public function profitAndLoss(int $tenantId, Carbon $from, Carbon $to, ?int $organizationId = null): array
    {
        $lines = $this->postedLines($tenantId, $from, $to, $organizationId);

        $revenue = collect($this->groupByAccount($lines, 'revenue'));
        $expenses = collect($this->groupByAccount($lines, 'expense'));

        $totalRevenue = TypedValue::float($revenue->sum('balance'));
        $totalExpenses = TypedValue::float($expenses->sum('balance'));

        return [
            'period_from' => $from->toDateString(),
            'period_to' => $to->toDateString(),
            'revenue' => $revenue,
            'expenses' => $expenses,
            'total_revenue' => $totalRevenue,
            'total_expenses' => $totalExpenses,
            'net_income' => $totalRevenue - $totalExpenses,
        ];
    }

    // ─────────────────────────────────────────────────────
    // Balance Sheet
    // ─────────────────────────────────────────────────────

    /** @return array<string, mixed> */
    public function balanceSheet(int $tenantId, Carbon $asOf, ?int $organizationId = null): array
    {
        $lines = $this->postedLines($tenantId, Carbon::parse('1970-01-01'), $asOf, $organizationId);

        $assets = collect($this->groupByAccount($lines, 'asset'));
        $liabilities = collect($this->groupByAccount($lines, 'liability'));
        $equity = collect($this->groupByAccount($lines, 'equity'));

        $totalAssets = TypedValue::float($assets->sum('balance'));
        $totalLiabilities = TypedValue::float($liabilities->sum('balance'));
        $totalEquity = TypedValue::float($equity->sum('balance'));

        return [
            'as_of' => $asOf->toDateString(),
            'assets' => $assets,
            'liabilities' => $liabilities,
            'equity' => $equity,
            'total_assets' => $totalAssets,
            'total_liabilities' => $totalLiabilities,
            'total_equity' => $totalEquity,
            'is_balanced' => abs($totalAssets - ($totalLiabilities + $totalEquity)) < 0.01,
        ];
    }

    // ─────────────────────────────────────────────────────
    // Cash Flow (simplified — direct method)
    // ─────────────────────────────────────────────────────

    /** @return array<string, mixed> */
    public function cashFlow(int $tenantId, Carbon $from, Carbon $to, ?int $organizationId = null): array
    {
        $lines = $this->postedLines($tenantId, $from, $to, $organizationId);

        // Cash accounts = asset accounts flagged as bank accounts or whose name hints at cash
        $cashAccountIds = ChartOfAccount::withoutTenantScope()
            ->where('tenant_id', $tenantId)
            ->where(fn ($q) => $q->where('is_bank_account', true)
                ->orWhereRaw("LOWER(name) LIKE '%kas%'")
                ->orWhereRaw("LOWER(name) LIKE '%cash%'"))
            ->pluck('id')
            ->toArray();

        $cashLines = $lines->whereIn('chart_of_account_id', $cashAccountIds);

        // Simplified: bucket by CoA category string
        $operating = collect($this->groupByCashFlow($cashLines, 'operating'));
        $investing = collect($this->groupByCashFlow($cashLines, 'investing'));
        $financing = collect($this->groupByCashFlow($cashLines, 'financing'));

        // Everything not tagged → operating
        $taggedAccountIds = $operating->pluck('id')
            ->merge($investing->pluck('id'))
            ->merge($financing->pluck('id'))
            ->unique()
            ->toArray();

        $untagged = $cashLines->whereNotIn('chart_of_account_id', $taggedAccountIds);
        $operatingFinal = $this->groupByAccount($untagged->merge($cashLines->whereIn('chart_of_account_id', $operating->pluck('id'))), 'asset');

        return [
            'period_from' => $from->toDateString(),
            'period_to' => $to->toDateString(),
            'operating' => $operating->isNotEmpty() ? $operating : collect($this->groupByAccount($cashLines, 'asset')),
            'investing' => $investing,
            'financing' => $financing,
            'net_cash' => $cashLines->sum(fn ($l) => (float) $l->debit - (float) $l->credit),
        ];
    }

    // ─────────────────────────────────────────────────────
    // Internals
    // ─────────────────────────────────────────────────────

    /** @return Collection<int, JournalEntryLine> */
    private function postedLines(int $tenantId, Carbon $from, Carbon $to, ?int $organizationId): Collection
    {
        return JournalEntryLine::query()
            ->with('chartOfAccount')
            ->where('tenant_id', $tenantId)
            ->whereHas('journalEntry', function (Builder $query) use ($tenantId, $from, $to, $organizationId): void {
                /** @var Builder<JournalEntry> $query */
                $query->where($query->getModel()->qualifyColumn('tenant_id'), $tenantId)
                    ->where('is_posted', true)
                    ->whereBetween('date', [$from->toDateString(), $to->toDateString()])
                    ->when($organizationId, fn (Builder $inner) => $inner->where($inner->getModel()->qualifyColumn('organization_id'), $organizationId));
            })
            ->get();
    }

    /**
     * @param  Collection<int, JournalEntryLine>  $lines
     * @return list<AccountSummary>
     */
    private function groupByAccount(Collection $lines, string $type): array
    {
        $creditNormal = in_array($type, ['revenue', 'liability', 'equity'], true);

        $grouped = $lines
            ->filter(fn ($l) => strtolower((string) $l->chartOfAccount?->type) === $type)
            ->groupBy('chart_of_account_id')
            ->map(function (Collection $group) use ($creditNormal): array {
                $firstLine = $group->first();
                if (! $firstLine instanceof JournalEntryLine) {
                    return [
                        'id' => null,
                        'code' => '',
                        'name' => 'Unknown',
                        'balance' => 0.0,
                    ];
                }

                $coa = $firstLine->chartOfAccount;
                $debit = $group->sum(fn ($l) => (float) $l->debit);
                $credit = $group->sum(fn ($l) => (float) $l->credit);
                $balance = $creditNormal ? ($credit - $debit) : ($debit - $credit);

                return [
                    'id' => $coa?->id !== null ? (int) $coa->id : null,
                    'code' => (string) ($coa->code ?? ''),
                    'name' => (string) ($coa->name ?? 'Unknown'),
                    'balance' => (float) round($balance, 2),
                ];
            })
            ->values()
            ->sortBy('code')
            ->values();

        return array_values($grouped->all());
    }

    /**
     * @param  Collection<int, JournalEntryLine>  $lines
     * @return list<AccountSummary>
     */
    private function groupByCashFlow(Collection $lines, string $category): array
    {
        $grouped = $lines
            ->filter(fn ($l) => strtolower((string) $l->chartOfAccount?->category) === $category)
            ->groupBy('chart_of_account_id')
            ->map(function (Collection $group): array {
                $firstLine = $group->first();
                if (! $firstLine instanceof JournalEntryLine) {
                    return [
                        'id' => null,
                        'code' => '',
                        'name' => 'Unknown',
                        'balance' => 0.0,
                    ];
                }

                $coa = $firstLine->chartOfAccount;

                return [
                    'id' => $coa?->id !== null ? (int) $coa->id : null,
                    'code' => (string) ($coa->code ?? ''),
                    'name' => (string) ($coa->name ?? 'Unknown'),
                    'balance' => (float) round(
                        $group->sum(fn ($l) => (float) $l->debit) - $group->sum(fn ($l) => (float) $l->credit),
                        2,
                    ),
                ];
            })
            ->values();

        return array_values($grouped->all());
    }
}
