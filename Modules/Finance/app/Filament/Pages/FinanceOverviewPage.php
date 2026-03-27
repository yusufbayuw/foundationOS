<?php

namespace Modules\Finance\Filament\Pages;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Facades\Filament;
use Filament\Pages\Page;
use Modules\Core\Support\FilamentUi;
use Modules\Finance\Models\Budget;
use Modules\Finance\Models\JournalEntry;
use Modules\Finance\Models\Payment;
use Modules\Finance\Models\StudentInvoice;

class FinanceOverviewPage extends Page
{
    use HasPageShield;

    protected static string $routePath = '/finance/overview';

    protected string $view = 'finance::filament.pages.finance-overview';

    protected static ?int $navigationSort = 5;

    public static function getNavigationLabel(): string
    {
        return 'Finance Overview';
    }

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return FilamentUi::module('Finance');
    }

    public function getStats(): array
    {
        $tenant = Filament::getTenant();
        $tenantId = $tenant?->getKey();

        if (! $tenantId) {
            return [];
        }

        return [
            'budgets_draft' => Budget::query()->where('tenant_id', $tenantId)->where('status', 'draft')->count(),
            'budgets_in_review' => Budget::query()->where('tenant_id', $tenantId)->whereIn('status', ['submitted', 'in_review', 'revision_required'])->count(),
            'invoices_draft' => StudentInvoice::query()->where('tenant_id', $tenantId)->where('status', 'draft')->count(),
            'invoices_issued' => StudentInvoice::query()->where('tenant_id', $tenantId)->whereIn('status', ['issued', 'partial'])->count(),
            'invoices_paid' => StudentInvoice::query()->where('tenant_id', $tenantId)->where('status', 'paid')->count(),
            'payments_pending' => Payment::query()->where('tenant_id', $tenantId)->where('status', 'pending')->count(),
            'payments_verified' => Payment::query()->where('tenant_id', $tenantId)->where('status', 'verified')->count(),
            'journals_posted' => JournalEntry::query()->where('tenant_id', $tenantId)->where('is_posted', true)->count(),
            'budgets_approved' => Budget::query()->where('tenant_id', $tenantId)->where('status', 'approved')->count(),
            'budget_allocated_amount' => (float) Budget::query()->where('tenant_id', $tenantId)->sum('allocated_amount'),
            'budget_remaining_amount' => (float) Budget::query()->where('tenant_id', $tenantId)->sum('remaining_amount'),
            'invoice_outstanding_amount' => (float) StudentInvoice::query()->where('tenant_id', $tenantId)->sum('remaining_amount'),
            'payment_verified_amount' => (float) Payment::query()->where('tenant_id', $tenantId)->where('status', 'verified')->sum('amount'),
        ];
    }
}
