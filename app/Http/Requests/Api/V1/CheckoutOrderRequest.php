<?php

namespace App\Http\Requests\Api\V1;

use App\Support\CurrentTenant;
use Illuminate\Validation\Rule;
use Modules\Core\Models\Organization;
use Modules\MerchOrder\Models\MerchOrder;

class CheckoutOrderRequest extends ApiRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', MerchOrder::class) ?? false;
    }

    /** @return array<string, array<int, mixed>> */
    public function rules(CurrentTenant $currentTenant): array
    {
        return [
            'organization_id' => [
                'nullable',
                'integer',
                Rule::exists(Organization::class, 'id')->where('tenant_id', $currentTenant->id()),
            ],
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
