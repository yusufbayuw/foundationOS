<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Validation\Rule;

class PaymentWebhookRequest extends ApiRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'order_id' => ['required', 'string', 'max:100'],
            'transaction_status' => [
                'required',
                'string',
                Rule::in(['capture', 'settlement', 'pending', 'deny', 'cancel', 'expire', 'failure']),
            ],
            'transaction_id' => ['nullable', 'string', 'max:100'],
            'status_code' => ['required', 'string', 'max:10'],
            'gross_amount' => ['required', 'numeric', 'decimal:2', 'min:0'],
            'currency' => ['required', 'string', 'size:3', 'uppercase'],
            'signature_key' => ['required', 'string', 'regex:/\A[a-f0-9]{128}\z/i'],
            'fraud_status' => ['nullable', 'string', Rule::in(['accept', 'challenge', 'deny'])],
            'payment_type' => ['nullable', 'string', 'max:50'],
        ];
    }
}
