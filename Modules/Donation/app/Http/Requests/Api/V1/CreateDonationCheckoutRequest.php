<?php

namespace Modules\Donation\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class CreateDonationCheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'campaign_id' => ['required', 'integer', 'exists:campaigns,id'],
            'amount' => ['required', 'numeric', 'min:1'],
            'donor.name' => ['nullable', 'string', 'max:255'],
            'donor.email' => ['nullable', 'email', 'max:255'],
            'donor.phone' => ['nullable', 'string', 'max:255'],
            'donor.is_anonymous' => ['nullable', 'boolean'],
        ];
    }
}
