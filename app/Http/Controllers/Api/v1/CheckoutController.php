<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Requests\Api\V1\CheckoutOrderRequest;
use App\Services\Checkout\CheckoutPaymentService;
use App\Support\CurrentTenant;
use Illuminate\Http\JsonResponse;

class CheckoutController extends ApiController
{
    public function __construct(
        private readonly CheckoutPaymentService $checkoutPayment,
        private readonly CurrentTenant $currentTenant,
    ) {}

    public function store(CheckoutOrderRequest $request): JsonResponse
    {
        $checkout = $this->checkoutPayment->checkoutMerchOrder((int) $this->currentTenant->id(), $request->validated());

        return $this->success([
            'order_id' => $checkout['order']->id,
            'payment_id' => $checkout['payment']->id,
            'payment_reference' => $checkout['order']->payment_reference,
            'status' => $checkout['order']->status,
            'redirect_url' => $checkout['transaction']['redirect_url'] ?? null,
        ], 201);
    }
}
