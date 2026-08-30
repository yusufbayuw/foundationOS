<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Requests\Api\V1\PaymentWebhookRequest;
use App\Services\Billing\MidtransWebhookException;
use App\Services\Billing\MidtransWebhookVerifier;
use App\Services\Checkout\CheckoutPaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Modules\Finance\Models\Payment;

class PaymentWebhookController extends ApiController implements HasMiddleware
{
    public function __construct(
        private readonly CheckoutPaymentService $checkoutPayment,
        private readonly MidtransWebhookVerifier $webhookVerifier,
    ) {}

    /** @return array<int, string> */
    public static function middleware(): array
    {
        return ['throttle:webhooks'];
    }

    public function __invoke(PaymentWebhookRequest $request): JsonResponse
    {
        if (blank(config('midtrans.server_key'))) {
            return $this->error(
                'payment_webhook_unavailable',
                'Payment webhook verification is not configured.',
                503,
            );
        }

        $payload = $request->validated();

        try {
            $this->webhookVerifier->verifySignature($payload);
        } catch (MidtransWebhookException $exception) {
            report($exception);

            return $this->error('invalid_payment_webhook', 'Invalid payment webhook notification.');
        }

        $payment = Payment::withoutTenantScope()
            ->where('payment_reference', $payload['order_id'])
            ->first();

        if (! $payment) {
            return $this->error('payment_reference_not_found', 'Payment reference was not found.', 404);
        }

        if (bccomp((string) $payment->amount, (string) $payload['gross_amount'], 2) !== 0) {
            return $this->error('payment_amount_mismatch', 'Payment amount does not match.', 400);
        }

        if (strtoupper((string) config('midtrans.currency', 'IDR')) !== $payload['currency']) {
            return $this->error('payment_currency_mismatch', 'Payment currency does not match.', 400);
        }

        if ($payload['transaction_status'] === 'capture' && ($payload['fraud_status'] ?? null) !== 'accept') {
            return $this->error('payment_fraud_status_invalid', 'Payment fraud status is not accepted.', 400);
        }

        $order = $this->checkoutPayment->handleWebhook($payload);

        if (! $order) {
            return $this->error('payment_reference_not_found', 'Payment reference was not found.', 404);
        }

        return $this->success([
            'order_id' => $order->id,
            'status' => $order->status,
        ]);
    }
}
