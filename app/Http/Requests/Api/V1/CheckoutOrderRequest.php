<?php

namespace App\Http\Requests\Api\V1;

class CheckoutOrderRequest extends ApiRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, array<int, string>> */
    public function rules(): array
    {
        return [
            'organization_id' => ['nullable', 'integer'],
            'code' => ['nullable', 'string', 'max:50'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_method' => ['nullable', 'string', 'max:50'],
            'payment_channel' => ['nullable', 'string', 'max:50'],
            'customer' => ['nullable', 'array'],
            'metadata' => ['nullable', 'array'],
        ];
    }
}
