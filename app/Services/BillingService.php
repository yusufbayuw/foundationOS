<?php

namespace App\Services;

use Illuminate\Support\Str;
use Midtrans\Config as MidtransConfig;
use Midtrans\Snap;
use Modules\Core\Models\SubscriptionLog;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\TenantModule;

class BillingService
{
    public function __construct()
    {
        MidtransConfig::$serverKey = config('midtrans.server_key');
        MidtransConfig::$isProduction = config('midtrans.is_production');
        MidtransConfig::$isSanitized = config('midtrans.is_sanitized');
        MidtransConfig::$is3ds = config('midtrans.is_3ds');
    }

    public function calculateMonthlyAmount(Tenant $tenant): array
    {
        $plan = $tenant->subscriptionPlan;

        if (! $plan) {
            return [
                'base' => 0,
                'seats' => 0,
                'modules' => 0,
                'total' => 0,
                'active_seats' => 0,
                'active_modules' => 0,
            ];
        }

        $activeSeats = $tenant->users()->count();
        $activeModules = TenantModule::where('tenant_id', $tenant->getKey())
            ->where('is_enabled', true)
            ->count();

        $billableSeats = max(0, $activeSeats - $plan->free_seats);
        $billableModules = max(0, $activeModules - $plan->free_modules);

        $seatAmount = $billableSeats * (float) $plan->price_per_seat;
        $moduleAmount = $billableModules * (float) $plan->price_per_module;
        $base = (float) $plan->price_monthly;

        return [
            'base' => $base,
            'seats' => $seatAmount,
            'modules' => $moduleAmount,
            'total' => $base + $seatAmount + $moduleAmount,
            'active_seats' => $activeSeats,
            'active_modules' => $activeModules,
        ];
    }

    public function createSnapPayment(Tenant $tenant, SubscriptionLog $invoice): string
    {
        $adminUser = $tenant->users()->first();

        $params = [
            'transaction_details' => [
                'order_id' => $invoice->invoice_number,
                'gross_amount' => (int) round($invoice->amount),
            ],
            'customer_details' => [
                'first_name' => $tenant->name,
                'email' => $adminUser?->email ?? 'noreply@foundationos.app',
            ],
            'item_details' => $this->buildItemDetails($tenant, $invoice),
            'callbacks' => [
                'finish' => route('billing.finish', ['tenant' => $tenant->getKey()]),
            ],
        ];

        $snapToken = Snap::getSnapToken($params);

        $invoice->update([
            'invoice_url' => "https://app.midtrans.com/snap/v2/vtweb/{$snapToken}",
            'metadata' => array_merge($invoice->metadata ?? [], ['snap_token' => $snapToken]),
        ]);

        return $snapToken;
    }

    public function generateInvoice(Tenant $tenant): SubscriptionLog
    {
        $amounts = $this->calculateMonthlyAmount($tenant);
        $invoiceNumber = 'INV-'.strtoupper($tenant->code).'-'.now()->format('Ym').'-'.strtoupper(Str::random(4));
        $periodStart = now()->startOfMonth();
        $periodEnd = now()->endOfMonth();

        return SubscriptionLog::create([
            'tenant_id' => $tenant->getKey(),
            'action' => 'monthly_invoice',
            'new_plan_id' => $tenant->subscription_plan_id,
            'amount' => $amounts['total'],
            'currency' => $tenant->currency ?: config('midtrans.currency', 'IDR'),
            'payment_status' => 'pending',
            'invoice_number' => $invoiceNumber,
            'period_start' => $periodStart,
            'period_end' => $periodEnd,
            'metadata' => [
                'base_amount' => $amounts['base'],
                'seat_amount' => $amounts['seats'],
                'module_amount' => $amounts['modules'],
                'active_seats' => $amounts['active_seats'],
                'active_modules' => $amounts['active_modules'],
            ],
        ]);
    }

    public function handleWebhookNotification(array $notification): void
    {
        $orderId = $notification['order_id'] ?? null;
        if (! $orderId) {
            return;
        }

        $invoice = SubscriptionLog::where('invoice_number', $orderId)->first();
        if (! $invoice) {
            return;
        }

        $transactionStatus = $notification['transaction_status'] ?? '';
        $fraudStatus = $notification['fraud_status'] ?? '';

        $paymentStatus = match (true) {
            $transactionStatus === 'capture' && $fraudStatus === 'accept' => 'paid',
            $transactionStatus === 'settlement' => 'paid',
            $transactionStatus === 'pending' => 'pending',
            in_array($transactionStatus, ['deny', 'cancel', 'expire']) => 'failed',
            default => $invoice->payment_status,
        };

        $invoice->update([
            'payment_status' => $paymentStatus,
            'payment_method' => $notification['payment_type'] ?? $invoice->payment_method,
            'metadata' => array_merge($invoice->metadata ?? [], [
                'midtrans_transaction_id' => $notification['transaction_id'] ?? null,
                'midtrans_status' => $transactionStatus,
            ]),
        ]);

        if ($paymentStatus === 'paid') {
            $this->activateTenantSubscription($invoice->tenant, $invoice);
        }
    }

    private function activateTenantSubscription(Tenant $tenant, SubscriptionLog $invoice): void
    {
        $tenant->update([
            'status' => 'active',
            'subscribed_at' => now(),
            'subscription_expires_at' => $invoice->period_end,
            'grace_period_ends_at' => null,
        ]);
    }

    private function buildItemDetails(Tenant $tenant, SubscriptionLog $invoice): array
    {
        $meta = $invoice->metadata ?? [];
        $items = [];

        if (($meta['base_amount'] ?? 0) > 0) {
            $items[] = [
                'id' => 'base',
                'price' => (int) round($meta['base_amount']),
                'quantity' => 1,
                'name' => 'Base Plan: '.($tenant->subscriptionPlan?->name ?? 'Plan'),
            ];
        }

        if (($meta['seat_amount'] ?? 0) > 0) {
            $items[] = [
                'id' => 'seats',
                'price' => (int) round($meta['seat_amount']),
                'quantity' => 1,
                'name' => "User Seats ({$meta['active_seats']} users)",
            ];
        }

        if (($meta['module_amount'] ?? 0) > 0) {
            $items[] = [
                'id' => 'modules',
                'price' => (int) round($meta['module_amount']),
                'quantity' => 1,
                'name' => "Active Modules ({$meta['active_modules']} modules)",
            ];
        }

        if (empty($items)) {
            $items[] = [
                'id' => 'subscription',
                'price' => (int) round($invoice->amount),
                'quantity' => 1,
                'name' => 'Monthly Subscription',
            ];
        }

        return $items;
    }
}
