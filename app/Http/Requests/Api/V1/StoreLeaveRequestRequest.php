<?php

namespace App\Http\Requests\Api\V1;

class StoreLeaveRequestRequest extends ApiRequest
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
            'employee_id' => ['required', 'integer'],
            'leave_type' => ['required', 'string', 'max:50'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'total_days' => ['required', 'integer', 'min:1'],
            'reason' => ['required', 'string'],
            'substitute_employee_id' => ['nullable', 'integer'],
        ];
    }
}
