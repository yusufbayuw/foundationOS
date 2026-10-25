<?php

namespace Modules\Finance\Services;

use App\Scopes\TenantScope;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Modules\Finance\Models\ChartOfAccount;
use Modules\Finance\Models\JournalEntry;
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
        $this->assertTenantReportAccess($tenantId);

        // Read the posted ledger directly rather than relying on nested
        // relationship scopes that can silently exclude valid payroll lines.
        // Every join enforces tenant equality; soft-deleted ledger records
        // and accounts remain excluded from financial statements.
        $balances = DB::table('journal_entry_lines as line')
            ->join('journal_entries as entry', function (JoinClause $join): void {
                $join->on('entry.id', '=', 'line.journal_entry_id')
                    ->on('entry.tenant_id', '=', 'line.tenant_id');
            })
            ->join('chart_of_accounts as account', function (JoinClause $join): void {
                $join->on('account.id', '=', 'line.chart_of_account_id')
                    ->on('account.tenant_id', '=', 'line.tenant_id');
            })
            ->where('line.tenant_id', $tenantId)
            ->where('entry.tenant_id', $tenantId)
            ->where('account.tenant_id', $tenantId)
            ->where('entry.is_posted', true)
            ->whereBetween('entry.date', [$from->toDateString(), $to->toDateString()])
            ->when($organizationId !== null, fn ($query) => $query->where('entry.organization_id', $organizationId))
            ->whereNull('line.deleted_at')
            ->whereNull('entry.deleted_at')
            ->whereNull('account.deleted_at')
            ->select('account.id', 'account.code', 'account.name', 'account.type')
            ->selectRaw('SUM(line.debit) as total_debit, SUM(line.credit) as total_credit')
            ->groupBy('account.id', 'account.code', 'account.name', 'account.type')
            ->get();

        $group = static function (string $type) use ($balances): Collection {
            return $balances
                ->filter(fn ($account): bool => strtolower((string) $account->type) === $type)
                ->map(static function ($account) use ($type): object {
                    $debit = (float) $account->total_debit;
                    $credit = (float) $account->total_credit;

                    return (object) [
                        'id' => $account->id,
                        'code' => $account->code,
                        'name' => $account->name,
                        'balance' => round($type === 'expense' ? $debit - $credit : $credit - $debit, 2),
                    ];
                })
                ->sortBy('code')
                ->values();
        };

        $revenue = $group('revenue');
        $expenses = $group('expense');

        $totalRevenue = (float) $revenue->sum('balance');
        $totalExpenses = (float) $expenses->sum('balance');

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

    private function assertTenantReportAccess(int $tenantId): void
    {
        $activeTenantId = TenantScope::resolveTenantId();

        if ($activeTenantId !== null && (string) $activeTenantId !== (string) $tenantId) {
            throw new AuthorizationException('Cannot access another tenant\'s financial reports.');
        }
    }

    /** Load all posted journal entry lines in the date range. */
    private function postedLines(int $tenantId, Carbon $from, Carbon $to, ?int $organizationId): Collection
    {
        $this->assertTenantReportAccess($tenantId);

        // Reports receive an explicit tenant ID (including CLI/export callers).
        // Do not let an unrelated ambient tenant scope hide a valid ledger entry.
        // Keep soft-delete scopes intact while restricting both sides of the
        // journal relationship and the chart of accounts to the requested tenant.
        $postedJournalIds = JournalEntry::withoutTenantScope()
            ->where('tenant_id', $tenantId)
            ->where('is_posted', true)
            ->whereBetween('date', [$from->toDateString(), $to->toDateString()])
            ->when($organizationId !== null, fn ($query) => $query->where('organization_id', $organizationId))
            ->select('id');

        return JournalEntryLine::withoutTenantScope()
            ->where('tenant_id', $tenantId)
            ->whereIn('journal_entry_id', $postedJournalIds)
            ->whereHas('chartOfAccount', fn ($query) => $query
                ->withoutGlobalScope(TenantScope::class)
                ->where('tenant_id', $tenantId))
            ->with(['chartOfAccount' => fn ($query) => $query
                ->withoutGlobalScope(TenantScope::class)
                ->where('tenant_id', $tenantId)])
            ->get();
    }

    /**
     * Group lines by CoA and compute net balance for a given account type.
     * Revenue / Liability / Equity → credit-normal (balance = credit − debit)
     * Asset / Expense → debit-normal (balance = debit − credit)
     *
     * @return Collection<int, object{code:string, name:string, balance:float}>
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
                    'code' => $coa?->code ?? '',
                    'name' => $coa?->name ?? 'Unknown',
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
                    'code' => $coa?->code ?? '',
                    'name' => $coa?->name ?? 'Unknown',
                    'balance' => round(
                        $group->sum(fn ($l) => (float) $l->debit) - $group->sum(fn ($l) => (float) $l->credit),
                        2,
                    ),
                ];
            })
            ->values();
    }
}
