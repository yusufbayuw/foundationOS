<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Requests\Api\V1\StorePaymentRequest;
use App\Services\WebhookDispatcher;
use App\Support\CurrentTenant;
use Illuminate\Http\JsonResponse;
use Modules\Finance\Models\Payment;

class PaymentController extends ApiController
{
    public function __construct(
        private readonly WebhookDispatcher $webhooks,
        private readonly CurrentTenant $currentTenant,
    ) {}

    public function store(StorePaymentRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $tenantId = $this->currentTenant->id();

        $payment = Payment::create(array_merge($validated, [
            'tenant_id' => $tenantId,
            'status' => $validated['status'] ?? 'pending',
        ]));

        if ($tenantId && $payment->status === 'verified') {
            $this->webhooks->dispatch((int) $tenantId, 'payment.verified', [
                'resource' => 'payment',
                'id' => $payment->id,
                'payment_number' => $payment->payment_number,
                'amount' => $payment->amount,
                'status' => $payment->status,
            ]);
        }

        return $this->success([
            'id' => $payment->id,
            'payment_number' => $payment->payment_number,
            'payment_date' => $payment->payment_date?->toDateString(),
            'amount' => $payment->amount,
            'status' => $payment->status,
            'created_at' => $payment->created_at?->toIso8601String(),
        ], 201);
    }
}
