<?php

namespace App\Http\Controllers\Api\v1;

use App\Services\Checkout\CheckoutPaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentWebhookController extends ApiController
{
    public function __construct(private readonly CheckoutPaymentService $checkoutPayment) {}

    public function __invoke(Request $request): JsonResponse
    {
        $order = $this->checkoutPayment->handleWebhook($request->all());

        if (! $order) {
            return $this->error('payment_reference_not_found', 'Payment reference was not found.', 404);
        }

        return $this->success([
            'order_id' => $order->id,
            'status' => $order->status,
        ]);
    }
}
