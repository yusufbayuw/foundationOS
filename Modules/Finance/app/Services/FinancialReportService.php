<?php

namespace Modules\Finance\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Modules\Finance\Models\ChartOfAccount;
use Modules\Finance\Models\JournalEntryLine;

/**
 * Generates P&L, Balance Sheet, and Cash Flow reports by aggregating
 * posted JournalEntryLine records grouped by ChartOfAccount.type.
 *
 * Account type conventions (stored as lowercase strings in the DB):
 *   asset, liability, equity, revenue, expense
 */
class FinancialReportService
{
    // ─────────────────────────────────────────────────────
    // Profit & Loss
    // ─────────────────────────────────────────────────────

    /**
     * @return array{revenue:Collection, expenses:Collection, total_revenue:float, total_expenses:float, net_income:float, period_from:string, period_to:string}
     */
    public function profitAndLoss(int $tenantId, Carbon $from, Carbon $to, ?int $organizationId = null): array
    {
        $lines = $this->postedLines($tenantId, $from, $to, $organizationId);

        $revenue = $this->groupByAccount($lines, 'revenue');
        $expenses = $this->groupByAccount($lines, 'expense');

        $totalRevenue = $revenue->sum('balance');
        $totalExpenses = $expenses->sum('balance');

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

    /**
     * @return array{assets:Collection, liabilities:Collection, equity:Collection, total_assets:float, total_liabilities:float, total_equity:float, is_balanced:bool, as_of:string}
     */
    public function balanceSheet(int $tenantId, Carbon $asOf, ?int $organizationId = null): array
    {
        $lines = $this->postedLines($tenantId, Carbon::create(1970, 1, 1), $asOf, $organizationId);

        $assets = $this->groupByAccount($lines, 'asset');
        $liabilities = $this->groupByAccount($lines, 'liability');
        $equity = $this->groupByAccount($lines, 'equity');

        $totalAssets = $assets->sum('balance');
        $totalLiabilities = $liabilities->sum('balance');
        $totalEquity = $equity->sum('balance');

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

    /**
     * @return array{operating:Collection, investing:Collection, financing:Collection, net_cash:float, period_from:string, period_to:string}
     */
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
        $operating = $this->groupByCashFlow($cashLines, 'operating');
        $investing = $this->groupByCashFlow($cashLines, 'investing');
        $financing = $this->groupByCashFlow($cashLines, 'financing');

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
            'operating' => $operating->isNotEmpty() ? $operating : $this->groupByAccount($cashLines, 'asset'),
            'investing' => $investing,
            'financing' => $financing,
            'net_cash' => $cashLines->sum(fn ($l) => (float) $l->debit - (float) $l->credit),
        ];
    }

    // ─────────────────────────────────────────────────────
    // Internals
    // ─────────────────────────────────────────────────────

    /** Load all posted journal entry lines in the date range. */
    private function postedLines(int $tenantId, Carbon $from, Carbon $to, ?int $organizationId): Collection
    {
        return JournalEntryLine::query()
            ->with('chartOfAccount')
            ->where('tenant_id', $tenantId)
            ->whereHas('journalEntry', function ($q) use ($tenantId, $from, $to, $organizationId): void {
                $q->where('tenant_id', $tenantId)
                    ->where('is_posted', true)
                    ->whereBetween('date', [$from->toDateString(), $to->toDateString()])
                    ->when($organizationId, fn ($q2) => $q2->where('organization_id', $organizationId));
            })
            ->get();
    }

    /**
     * Group lines by CoA and compute net balance for a given account type.
     * Revenue / Liability / Equity → credit-normal (balance = credit − debit)
     * Asset / Expense → debit-normal (balance = debit − credit)
     *
     * @return Collection<int, object{id: mixed, code: string, name: string, balance: float}&\stdClass>
     */
    private function groupByAccount(Collection $lines, string $type): Collection
    {
        $creditNormal = in_array($type, ['revenue', 'liability', 'equity'], true);

        return $lines
            ->filter(fn ($l) => strtolower((string) $l->chartOfAccount?->type) === $type)
            ->groupBy('chart_of_account_id')
            ->map(function (Collection $group) use ($creditNormal): object {
                $coa = $group->first()->chartOfAccount;
                $debit = $group->sum(fn ($l) => (float) $l->debit);
                $credit = $group->sum(fn ($l) => (float) $l->credit);
                $balance = $creditNormal ? ($credit - $debit) : ($debit - $credit);

                return (object) [
                    'id' => $coa?->id,
                    'code' => (string) ($coa->code ?? ''),
                    'name' => (string) ($coa->name ?? 'Unknown'),
                    'balance' => round($balance, 2),
                ];
            })
            ->values()
            ->sortBy('code');
    }

    /** Group cash-flow lines by CoA category tag. */
    private function groupByCashFlow(Collection $lines, string $category): Collection
    {
        return $lines
            ->filter(fn ($l) => strtolower((string) $l->chartOfAccount?->category) === $category)
            ->groupBy('chart_of_account_id')
            ->map(function (Collection $group): object {
                $coa = $group->first()->chartOfAccount;

                return (object) [
                    'id' => $coa?->id,
                    'code' => $coa->code ?? '',
                    'name' => $coa->name ?? 'Unknown',
                    'balance' => round(
                        $group->sum(fn ($l) => (float) $l->debit) - $group->sum(fn ($l) => (float) $l->credit),
                        2,
                    ),
                ];
            })
            ->values();
    }
}
