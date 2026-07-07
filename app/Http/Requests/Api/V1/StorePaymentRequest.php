<?php

namespace App\Http\Requests\Api\V1;

class StorePaymentRequest extends ApiRequest
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
            'student_invoice_id' => ['required', 'integer'],
            'chart_of_account_id' => ['required', 'integer'],
            'payment_number' => ['required', 'string', 'max:50'],
            'payment_date' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'min:0'],
            'payment_method' => ['nullable', 'string', 'max:50'],
            'payment_channel' => ['nullable', 'string', 'max:50'],
            'reference_number' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'in:pending,verified,rejected'],
        ];
    }
}
