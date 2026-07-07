<?php

namespace App\Http\Requests\Api\V1;

use App\Support\CurrentTenant;
use Illuminate\Validation\Rule;

class StoreApplicantRequest extends ApiRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $tenantId = app(CurrentTenant::class)->id();

        return [
            'admission_period_id' => [
                'required',
                'integer',
                Rule::exists('admission_periods', 'id')->where('tenant_id', $tenantId),
            ],
            'registration_number' => ['required', 'string', 'max:50'],
            'full_name' => ['required', 'string', 'max:255'],
            'birth_date' => ['nullable', 'date'],
            'gender' => ['nullable', 'in:male,female'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email'],
            'nisn' => ['nullable', 'string', 'max:20'],
            'previous_school' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
