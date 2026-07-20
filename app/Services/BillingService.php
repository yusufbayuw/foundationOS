<?php

namespace App\Services;

use App\Services\Billing\MidtransWebhookVerifier;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Midtrans\Config as MidtransConfig;
use Midtrans\Snap;
use Modules\Core\Models\SubscriptionLog;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\TenantModule;

class BillingService
{
    public function __construct(private readonly MidtransWebhookVerifier $webhookVerifier)
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

    public function createSnapPayment(Tenant $tenant, SubscriptionLog $invoice, ?int $actorId = null): string
    {
        if ($invoice->invoice_url && (($invoice->metadata ?? [])['snap_token'] ?? null)) {
            return (string) (($invoice->metadata ?? [])['snap_token']);
        }

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

        $invoice->update([
            'metadata' => array_merge($invoice->metadata ?? [], [
                'payment_session_status' => 'preparing',
                'payment_session_requested_at' => now()->toISOString(),
                'payment_session_requested_by' => $actorId,
            ]),
        ]);

        $snapToken = Snap::getSnapToken($params);

        $invoice->update([
            'invoice_url' => "https://app.midtrans.com/snap/v2/vtweb/{$snapToken}",
            'processed_by' => $actorId,
            'metadata' => array_merge($invoice->metadata ?? [], [
                'snap_token' => $snapToken,
                'payment_session_status' => 'ready',
                'payment_session_ready_at' => now()->toISOString(),
                'payment_session_error' => null,
            ]),
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
        $this->webhookVerifier->verifySignature($notification);

        $orderId = $notification['order_id'] ?? null;
        if (! $orderId) {
            return;
        }

        DB::transaction(function () use ($notification, $orderId): void {
            $invoice = SubscriptionLog::query()
                ->where('invoice_number', $orderId)
                ->lockForUpdate()
                ->first();

            if (! $invoice) {
                return;
            }

            $metadata = $this->appendRawWebhookPayload($invoice->metadata ?? [], $notification);
            $webhookKey = $this->webhookKey($notification);

            if (in_array($webhookKey, $metadata['processed_midtrans_webhook_keys'] ?? [], true)) {
                return;
            }

            if (! $this->amountMatchesInvoice($invoice, $notification)) {
                $invoice->update([
                    'metadata' => array_merge($metadata, [
                        'midtrans_validation_failure' => 'amount_mismatch',
                    ]),
                ]);

                return;
            }

            if (! $this->currencyMatchesInvoice($invoice, $notification)) {
                $invoice->update([
                    'metadata' => array_merge($metadata, [
                        'midtrans_validation_failure' => 'currency_mismatch',
                    ]),
                ]);

                return;
            }

            $transactionStatus = (string) ($notification['transaction_status'] ?? '');
            $fraudStatus = (string) ($notification['fraud_status'] ?? '');

            $paymentStatus = match (true) {
                $transactionStatus === 'capture' && $fraudStatus === 'accept' => 'paid',
                $transactionStatus === 'settlement' => 'paid',
                $transactionStatus === 'pending' => 'pending',
                in_array($transactionStatus, ['deny', 'cancel', 'expire'], true) => 'failed',
                default => $invoice->payment_status,
            };

            $wasPaid = $invoice->payment_status === 'paid';
            $processedWebhookKeys = array_values(array_unique([
                ...($metadata['processed_midtrans_webhook_keys'] ?? []),
                $webhookKey,
            ]));

            $invoice->update([
                'payment_status' => $paymentStatus,
                'payment_method' => $notification['payment_type'] ?? $invoice->payment_method,
                'metadata' => array_merge($metadata, [
                    'midtrans_transaction_id' => $notification['transaction_id'] ?? null,
                    'midtrans_status' => $transactionStatus,
                    'midtrans_validation_failure' => null,
                    'processed_midtrans_webhook_keys' => $processedWebhookKeys,
                ]),
            ]);

            if ($paymentStatus === 'paid' && ! $wasPaid) {
                $this->activateTenantSubscription($invoice->tenant, $invoice);
            }
        });
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

    private function amountMatchesInvoice(SubscriptionLog $invoice, array $notification): bool
    {
        if (! array_key_exists('gross_amount', $notification)) {
            return false;
        }

        return abs(round((float) $invoice->amount, 2) - round((float) $notification['gross_amount'], 2)) < 0.01;
    }

    private function currencyMatchesInvoice(SubscriptionLog $invoice, array $notification): bool
    {
        if (blank($notification['currency'] ?? null)) {
            return true;
        }

        return strtoupper((string) $invoice->currency) === strtoupper((string) $notification['currency']);
    }

    private function webhookKey(array $notification): string
    {
        return hash('sha256', implode('|', [
            $notification['transaction_id'] ?? '',
            $notification['order_id'] ?? '',
            $notification['transaction_status'] ?? '',
            $notification['status_code'] ?? '',
            $notification['gross_amount'] ?? '',
        ]));
    }

    private function appendRawWebhookPayload(array $metadata, array $notification): array
    {
        $webhooks = $metadata['midtrans_webhooks'] ?? [];
        $webhookKey = $this->webhookKey($notification);

        foreach ($webhooks as $webhook) {
            if (($webhook['key'] ?? null) === $webhookKey) {
                return $metadata;
            }
        }

        $webhooks[] = [
            'key' => $webhookKey,
            'received_at' => now()->toIso8601String(),
            'payload' => $notification,
        ];

        $metadata['midtrans_webhooks'] = $webhooks;

        return $metadata;
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
