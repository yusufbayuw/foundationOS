<?php

namespace App\Http\Controllers\Api\v1;

use App\Services\WebhookDispatcher;
use App\Support\CurrentTenant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Modules\Finance\Models\Payment;

class PaymentController extends ApiController
{
    public function __construct(
        private readonly WebhookDispatcher $webhooks,
        private readonly CurrentTenant $currentTenant,
    ) {}

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'student_invoice_id' => ['required', 'integer'],
            'chart_of_account_id' => ['required', 'integer'],
            'payment_number' => ['required', 'string', 'max:50'],
            'payment_date' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'min:0'],
            'payment_method' => ['nullable', 'string', 'max:50'],
            'payment_channel' => ['nullable', 'string', 'max:50'],
            'reference_number' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'in:pending,verified,rejected'],
        ]);

        if ($validator->fails()) {
            return $this->error('validation_failed', 'The given data was invalid.', 422, $validator->errors()->toArray());
        }

        $tenantId = $this->currentTenant->id();

        $validated = $validator->validated();

        $payment = Payment::query()->create([
            'student_invoice_id' => $validated['student_invoice_id'],
            'chart_of_account_id' => $validated['chart_of_account_id'],
            'payment_number' => $validated['payment_number'],
            'payment_date' => $validated['payment_date'],
            'amount' => $validated['amount'],
            'payment_method' => $validated['payment_method'] ?? null,
            'payment_channel' => $validated['payment_channel'] ?? null,
            'reference_number' => $validated['reference_number'] ?? null,
            'tenant_id' => $tenantId,
            'status' => $validated['status'] ?? 'pending',
        ]);

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
            'payment_date' => $payment->payment_date->toDateString(),
            'amount' => $payment->amount,
            'status' => $payment->status,
            'created_at' => $payment->created_at?->toIso8601String(),
        ], 201);
    }
}
