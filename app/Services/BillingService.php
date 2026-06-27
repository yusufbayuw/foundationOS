<?php

namespace App\Services;

use App\Services\Billing\MidtransWebhookVerifier;
use App\Support\TypedValue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Midtrans\Config as MidtransConfig;
use Midtrans\Snap;
use Modules\Core\Models\SubscriptionLog;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\TenantModule;
use Modules\Core\Models\User;

class BillingService
{
    public function __construct(private readonly MidtransWebhookVerifier $webhookVerifier)
    {
        MidtransConfig::$serverKey = config('midtrans.server_key');
        MidtransConfig::$isProduction = config('midtrans.is_production');
        MidtransConfig::$isSanitized = config('midtrans.is_sanitized');
        MidtransConfig::$is3ds = config('midtrans.is_3ds');
    }

    /**
     * @return array{
     *     base: float,
     *     seats: float,
     *     modules: float,
     *     total: float,
     *     active_seats: int,
     *     active_modules: int
     * }
     */
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
        /** @var User|null $adminUser */
        $adminUser = $tenant->users()->first();

        $params = [
            'transaction_details' => [
                'order_id' => $invoice->invoice_number,
                'gross_amount' => (int) round((float) $invoice->amount),
            ],
            'customer_details' => [
                'first_name' => $tenant->name,
                'email' => $adminUser !== null ? $adminUser->email : 'noreply@foundationos.app',
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

    /**
     * @param  array<string, mixed>  $notification
     */
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

            $metadata = $this->appendRawWebhookPayload($this->normalizeMetadata($invoice->metadata), $notification);
            $webhookKey = $this->webhookKey($notification);

            /** @var list<string> $existingWebhookKeys */
            $existingWebhookKeys = is_array($metadata['processed_midtrans_webhook_keys'] ?? null)
                ? array_values(array_filter(
                    $metadata['processed_midtrans_webhook_keys'],
                    static fn (mixed $key): bool => is_string($key) && $key !== '',
                ))
                : [];

            if (in_array($webhookKey, $existingWebhookKeys, true)) {
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

            $transactionStatus = TypedValue::string($notification['transaction_status'] ?? null);
            $fraudStatus = TypedValue::string($notification['fraud_status'] ?? null);

            $paymentStatus = match (true) {
                $transactionStatus === 'capture' && $fraudStatus === 'accept' => 'paid',
                $transactionStatus === 'settlement' => 'paid',
                $transactionStatus === 'pending' => 'pending',
                in_array($transactionStatus, ['deny', 'cancel', 'expire'], true) => 'failed',
                default => $invoice->payment_status,
            };

            $wasPaid = $invoice->payment_status === 'paid';
            $processedWebhookKeys = array_values(array_unique([
                ...$existingWebhookKeys,
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
                $tenant = $invoice->tenant;
                if ($tenant instanceof Tenant) {
                    $this->activateTenantSubscription($tenant, $invoice);
                }
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

    /**
     * @param  array<string, mixed>  $notification
     */
    private function amountMatchesInvoice(SubscriptionLog $invoice, array $notification): bool
    {
        if (! array_key_exists('gross_amount', $notification)) {
            return false;
        }

        return abs(round((float) $invoice->amount, 2) - round(TypedValue::float($notification['gross_amount']), 2)) < 0.01;
    }

    /**
     * @param  array<string, mixed>  $notification
     */
    private function currencyMatchesInvoice(SubscriptionLog $invoice, array $notification): bool
    {
        if (blank($notification['currency'] ?? null)) {
            return true;
        }

        return strtoupper((string) $invoice->currency) === strtoupper(TypedValue::string($notification['currency']));
    }

    /**
     * @param  array<string, mixed>  $notification
     */
    private function webhookKey(array $notification): string
    {
        return hash('sha256', implode('|', [
            TypedValue::string($notification['transaction_id'] ?? null),
            TypedValue::string($notification['order_id'] ?? null),
            TypedValue::string($notification['transaction_status'] ?? null),
            TypedValue::string($notification['status_code'] ?? null),
            TypedValue::string($notification['gross_amount'] ?? null),
        ]));
    }

    /**
     * @param  array<string, mixed>  $metadata
     * @param  array<string, mixed>  $notification
     * @return array<string, mixed>
     */
    private function appendRawWebhookPayload(array $metadata, array $notification): array
    {
        /** @var list<array{key: string, received_at: string, payload: array<string, mixed>}> $webhooks */
        $webhooks = is_array($metadata['midtrans_webhooks'] ?? null) ? $metadata['midtrans_webhooks'] : [];
        $webhookKey = $this->webhookKey($notification);

        foreach ($webhooks as $webhook) {
            if ($webhook['key'] === $webhookKey) {
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

    /**
     * @return list<array{id: string, price: int, quantity: int, name: string}>
     */
    private function buildItemDetails(Tenant $tenant, SubscriptionLog $invoice): array
    {
        $meta = $this->normalizeMetadata($invoice->metadata);
        $items = [];

        $baseAmount = TypedValue::float($meta['base_amount'] ?? 0);
        if ($baseAmount > 0) {
            $items[] = [
                'id' => 'base',
                'price' => (int) round($baseAmount),
                'quantity' => 1,
                'name' => 'Base Plan: '.($tenant->subscriptionPlan !== null ? $tenant->subscriptionPlan->name : 'Plan'),
            ];
        }

        $seatAmount = TypedValue::float($meta['seat_amount'] ?? 0);
        if ($seatAmount > 0) {
            $activeSeats = TypedValue::int($meta['active_seats'] ?? 0);
            $items[] = [
                'id' => 'seats',
                'price' => (int) round($seatAmount),
                'quantity' => 1,
                'name' => "User Seats ({$activeSeats} users)",
            ];
        }

        $moduleAmount = TypedValue::float($meta['module_amount'] ?? 0);
        if ($moduleAmount > 0) {
            $activeModules = TypedValue::int($meta['active_modules'] ?? 0);
            $items[] = [
                'id' => 'modules',
                'price' => (int) round($moduleAmount),
                'quantity' => 1,
                'name' => "Active Modules ({$activeModules} modules)",
            ];
        }

        if ($items === []) {
            $items[] = [
                'id' => 'subscription',
                'price' => (int) round((float) $invoice->amount),
                'quantity' => 1,
                'name' => 'Monthly Subscription',
            ];
        }

        return $items;
    }

    /**
     * @return array<string, mixed>
     */
    private function normalizeMetadata(mixed $metadata): array
    {
        if (! is_array($metadata)) {
            return [];
        }

        /** @var array<string, mixed> $metadata */

        return $metadata;
    }
}
