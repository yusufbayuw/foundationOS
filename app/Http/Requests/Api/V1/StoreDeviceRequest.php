<?php

namespace App\Http\Requests\Api\V1;

class StoreDeviceRequest extends ApiRequest
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
            'token' => ['required', 'string', 'max:500'],
            'platform' => ['required', 'in:android,ios,web'],
        ];
    }
}
