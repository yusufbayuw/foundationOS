<?php

namespace App\Filament\Pages;

use App\Jobs\CreateBillingSnapPaymentJob;
use App\Services\BillingService;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Modules\Core\Models\SubscriptionLog;
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

        if ($tenant) {
            $this->currency = $tenant->currency ?: 'IDR';
            $billing = app(BillingService::class);
            $this->currentAmounts = $billing->calculateMonthlyAmount($tenant);
        }
    }

    public function formatAmount(float $amount): string
    {
        return CurrencyFormatter::format($amount, $this->currency);
    }

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

    public function generateInvoiceAction(): Action
    {
        return Action::make('generateInvoice')
            ->label(FilamentUi::text('Generate Invoice'))
            ->icon('heroicon-o-document-plus')
            ->color('gray')
            ->size('sm')
            ->requiresConfirmation()
            ->action(function (): void {
                $this->generateInvoice();
            });
    }

    public function payInvoiceAction(): Action
    {
        return Action::make('payInvoice')
            ->label(FilamentUi::text('Pay'))
            ->icon('heroicon-o-credit-card')
            ->color('primary')
            ->size('xs')
            ->action(function (array $arguments): void {
                $invoiceId = (int) ($arguments['invoice'] ?? 0);

                if ($invoiceId <= 0) {
                    Notification::make()
                        ->title(FilamentUi::text('Payment error'))
                        ->body(FilamentUi::text('Invoice not found.'))
                        ->danger()
                        ->send();

                    return;
                }

                $this->payInvoice($invoiceId);
            });
    }

    public function payInvoice(int $invoiceId): void
    {
        $tenant = Filament::getTenant();

        if (! $tenant) {
            return;
        }

        $invoice = SubscriptionLog::where('tenant_id', $tenant->getKey())
            ->where('id', $invoiceId)
            ->whereIn('payment_status', ['pending', 'failed'])
            ->first();

        if (! $invoice) {
            Notification::make()
                ->title(FilamentUi::text('Payment error'))
                ->body(FilamentUi::text('Invoice not found.'))
                ->danger()
                ->send();

            return;
        }

        if ($invoice->invoice_url && (($invoice->metadata ?? [])['snap_token'] ?? null)) {
            $this->snapToken = (string) (($invoice->metadata ?? [])['snap_token']);
            $this->dispatch('open-midtrans-snap', token: $this->snapToken);

            Notification::make()
                ->title(FilamentUi::text('Payment session ready'))
                ->success()
                ->send();

            return;
        }

        $invoice->update([
            'metadata' => array_merge($invoice->metadata ?? [], [
                'payment_session_status' => 'queued',
                'payment_session_requested_at' => now()->toISOString(),
                'payment_session_requested_by' => Auth::id(),
            ]),
        ]);

        CreateBillingSnapPaymentJob::dispatch((int) $tenant->getKey(), (int) $invoice->getKey(), Auth::id());

        Notification::make()
            ->title(FilamentUi::text('Payment session is being prepared'))
            ->body(FilamentUi::text('Refresh will reveal the payment button when the Snap token is ready.'))
            ->success()
            ->send();
    }

    public function generateInvoice(): void
    {
        $tenant = Filament::getTenant();

        if (! $tenant) {
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
