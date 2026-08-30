<?php

namespace App\Http\Requests\Api\V1;

use App\Support\CurrentTenant;
use Illuminate\Validation\Rule;
use Modules\Employee\Models\Employee;
use Modules\Employee\Models\LeaveRequest;

class StoreLeaveRequestRequest extends ApiRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', LeaveRequest::class) ?? false;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(CurrentTenant $currentTenant): array
    {
        $tenantId = $currentTenant->id();

        return [
            'employee_id' => [
                'required',
                'integer',
                Rule::exists(Employee::class, 'id')->where('tenant_id', $tenantId),
            ],
            'leave_type' => ['required', 'string', 'max:50'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'total_days' => ['required', 'integer', 'min:1'],
            'reason' => ['required', 'string'],
            'substitute_employee_id' => [
                'nullable',
                'integer',
                Rule::exists(Employee::class, 'id')->where('tenant_id', $tenantId),
            ],
        ];
    }
}
