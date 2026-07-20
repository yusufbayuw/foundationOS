<?php

namespace App\Http\Requests\Api\V1;

use App\Models\Device;

class StoreDeviceRequest extends ApiRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Device::class) ?? false;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'token' => ['required', 'string', 'max:500'],
            'platform' => ['required', 'in:android,ios,web'],
        ];
    }
}
