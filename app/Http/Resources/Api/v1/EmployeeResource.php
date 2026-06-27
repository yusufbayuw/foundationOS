<?php

namespace App\Http\Resources\Api\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Employee\Models\Employee;

class EmployeeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var Employee $employee */
        $employee = $this->resource;

        return [
            'id' => $employee->id,
            'employee_number' => $employee->employee_number,
            'full_name' => $employee->full_name,
            'gender' => $employee->gender,
            'email' => $employee->email,
            'phone' => $employee->phone,
            'employment_type' => $employee->employment_type,
            'employment_status' => $employee->employment_status,
            'join_date' => $employee->join_date->toDateString(),
            'end_date' => $employee->end_date?->toDateString(),
            'organization_id' => $employee->organization_id,
            'department_id' => $employee->department_id,
            'position_id' => $employee->position_id,
            'user_id' => $employee->user_id,
            'organization' => $this->whenLoaded('organization', fn () => new OrganizationResource($employee->organization)),
            'created_at' => $employee->created_at?->toIso8601String(),
        ];
    }
}
