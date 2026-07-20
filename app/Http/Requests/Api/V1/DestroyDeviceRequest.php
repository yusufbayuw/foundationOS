<?php

namespace App\Http\Requests\Api\V1;

use App\Models\Device;

class DestroyDeviceRequest extends ApiRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('delete', Device::class) ?? false;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [];
    }
}
