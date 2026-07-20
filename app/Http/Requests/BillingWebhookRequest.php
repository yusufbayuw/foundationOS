<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BillingWebhookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'order_id' => ['required', 'string', 'max:100'],
            'transaction_status' => ['required', 'string', 'max:50'],
            'transaction_id' => ['nullable', 'string', 'max:100'],
            'status_code' => ['nullable', 'string', 'max:10'],
            'gross_amount' => ['required', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'size:3'],
            'signature_key' => ['nullable', 'string'],
        ];
    }
}
