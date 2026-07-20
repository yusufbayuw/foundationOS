<?php

namespace App\Services\Checkout;

use App\Payments\PaymentGateway;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Finance\Models\Payment;
use Modules\MerchOrder\Models\MerchOrder;

class CheckoutPaymentService
{
    public function __construct(private readonly PaymentGateway $paymentGateway) {}

    /**
     * @param  array<string, mixed>  $data
     * @return array{order: MerchOrder, payment: Payment, transaction: array<string, mixed>}
     */
    public function checkoutMerchOrder(int $tenantId, array $data): array
    {
        return DB::transaction(function () use ($tenantId, $data): array {
            $order = MerchOrder::query()->create([
                'tenant_id' => $tenantId,
                'organization_id' => $data['organization_id'] ?? null,
                'code' => $data['code'] ?? 'MO-'.Str::upper(Str::random(10)),
                'name' => $data['name'],
                'status' => 'pending_payment',
                'description' => $data['description'] ?? null,
                'total_amount' => $data['amount'],
                'meta' => ['checkout' => $data['metadata'] ?? []],
            ]);

            $transaction = $this->paymentGateway->createOrderTransaction($order, [
                'amount' => $data['amount'],
                'customer' => $data['customer'] ?? [],
                'metadata' => $data['metadata'] ?? [],
            ]);

            $reference = (string) $transaction['reference'];

            $order->forceFill(['payment_reference' => $reference])->save();

            $payment = Payment::query()->create([
                'tenant_id' => $tenantId,
                'merch_order_id' => $order->id,
                'payment_number' => 'PAY-'.$order->code,
                'payment_date' => now()->toDateString(),
                'amount' => $data['amount'],
                'payment_method' => $data['payment_method'] ?? 'gateway',
                'payment_channel' => $data['payment_channel'] ?? null,
                'reference_number' => $reference,
                'payment_reference' => $reference,
                'gateway_payload' => $transaction['payload'] ?? $transaction,
                'status' => $transaction['status'] ?? 'pending',
            ]);

            return compact('order', 'payment', 'transaction');
        });
    }

    /** @param  array<string, mixed>  $payload */
    public function handleWebhook(array $payload): ?MerchOrder
    {
        $reference = (string) ($payload['payment_reference'] ?? $payload['reference'] ?? $payload['order_id'] ?? '');

        if ($reference === '') {
            return null;
        }

        return DB::transaction(function () use ($payload, $reference): ?MerchOrder {
            $order = MerchOrder::withoutTenantScope()->where('payment_reference', $reference)->lockForUpdate()->first();
            if (! $order) {
                return null;
            }

            $payment = Payment::withoutTenantScope()->where('payment_reference', $reference)->lockForUpdate()->first();
            $status = $this->mapWebhookStatus((string) ($payload['status'] ?? $payload['transaction_status'] ?? ''));

            if ($payment && in_array($payment->status, ['verified', 'failed'], true)) {
                return $order;
            }

            if ($status === 'paid') {
                $order->forceFill(['status' => 'paid', 'paid_at' => $order->paid_at ?? now()])->save();
                $payment?->forceFill(['status' => 'verified', 'verified_at' => $payment->verified_at ?? now(), 'gateway_payload' => $payload])->save();
            } elseif ($status === 'failed') {
                $order->forceFill(['status' => 'payment_failed'])->save();
                $payment?->forceFill(['status' => 'failed', 'gateway_payload' => $payload])->save();
            }

            return $order;
        });
    }

    private function mapWebhookStatus(string $status): string
    {
        return match ($status) {
            'paid', 'settlement', 'capture', 'success' => 'paid',
            'failed', 'deny', 'cancel', 'expire', 'failure' => 'failed',
            default => 'pending',
        };
    }
}
