<?php

namespace App\Filament\Pages;

use App\Services\BillingService;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Collection;
use Modules\Core\Models\SubscriptionLog;
use Modules\Core\Models\Tenant;
use Modules\Core\Support\CurrencyFormatter;
use Modules\Core\Support\FilamentUi;

class BillingPage extends Page
{
    protected static \BackedEnum|string|null $navigationIcon = Heroicon::CreditCard;

    protected string $view = 'billing.page';

    protected static ?int $navigationSort = 99;

    public ?string $snapToken = null;

    public ?array $currentAmounts = null;

    public string $currency = 'IDR';

    public function getTitle(): string
    {
        return FilamentUi::text('Billing & Subscription');
    }

    public static function getNavigationLabel(): string
    {
        return FilamentUi::text('Billing');
    }

    public static function getNavigationGroup(): ?string
    {
        return null;
    }

    public function mount(): void
    {
        $tenant = Filament::getTenant();

        if ($tenant instanceof Tenant) {
            $this->currency = $tenant->currency ?: 'IDR';
            $billing = app(BillingService::class);
            $this->currentAmounts = $billing->calculateMonthlyAmount($tenant);
        }
    }

    public function formatAmount(float $amount): string
    {
        return CurrencyFormatter::format($amount, $this->currency);
    }

    /** @return Collection<int, SubscriptionLog> */
    public function getRecentInvoices(): Collection
    {
        $tenant = Filament::getTenant();

        if (! $tenant) {
            return collect();
        }

        return SubscriptionLog::where('tenant_id', $tenant->getKey())
            ->where('action', 'monthly_invoice')
            ->orderByDesc('created_at')
            ->limit(12)
            ->get();
    }

    public function payInvoice(int $invoiceId): void
    {
        $tenant = Filament::getTenant();

        if (! ($tenant instanceof Tenant)) {
            return;
        }

        $invoice = SubscriptionLog::where('tenant_id', $tenant->getKey())
            ->where('id', $invoiceId)
            ->whereIn('payment_status', ['pending', 'failed'])
            ->firstOrFail();

        try {
            $billing = app(BillingService::class);
            $this->snapToken = $billing->createSnapPayment($tenant, $invoice);
            $this->dispatch('open-midtrans-snap', token: $this->snapToken);
        } catch (\Exception $e) {
            Notification::make()
                ->title(FilamentUi::text('Payment error'))
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function generateInvoice(): void
    {
        $tenant = Filament::getTenant();

        if (! ($tenant instanceof Tenant)) {
            return;
        }

        $alreadyBilled = SubscriptionLog::where('tenant_id', $tenant->getKey())
            ->where('action', 'monthly_invoice')
            ->whereYear('period_start', now()->year)
            ->whereMonth('period_start', now()->month)
            ->exists();

        if ($alreadyBilled) {
            Notification::make()
                ->title(FilamentUi::text('Already billed'))
                ->warning()
                ->send();

            return;
        }

        $billing = app(BillingService::class);
        $billing->generateInvoice($tenant);
        $this->currentAmounts = $billing->calculateMonthlyAmount($tenant);

        Notification::make()
            ->title(FilamentUi::text('Invoice generated'))
            ->success()
            ->send();
    }
}
