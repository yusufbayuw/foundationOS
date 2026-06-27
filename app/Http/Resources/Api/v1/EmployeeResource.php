<?php

namespace App\Http\Resources\Api\v1;

use DateTimeInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Employee\Models\Employee;

class EmployeeResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var Employee $employee */
        $employee = $this->resource;
        $joinDate = $employee->getAttribute('join_date');
        $endDate = $employee->getAttribute('end_date');
        $createdAt = $employee->getAttribute('created_at');

        return [
            'id' => $employee->getKey(),
            'employee_number' => $employee->getAttribute('employee_number'),
            'full_name' => $employee->getAttribute('full_name'),
            'gender' => $employee->getAttribute('gender'),
            'email' => $employee->getAttribute('email'),
            'phone' => $employee->getAttribute('phone'),
            'employment_type' => $employee->getAttribute('employment_type'),
            'employment_status' => $employee->getAttribute('employment_status'),
            'join_date' => $joinDate instanceof DateTimeInterface ? $joinDate->format('Y-m-d') : null,
            'end_date' => $endDate instanceof DateTimeInterface ? $endDate->format('Y-m-d') : null,
            'organization_id' => $employee->getAttribute('organization_id'),
            'department_id' => $employee->getAttribute('department_id'),
            'position_id' => $employee->getAttribute('position_id'),
            'user_id' => $employee->getAttribute('user_id'),
            'organization' => $this->whenLoaded('organization', fn () => new OrganizationResource($employee->organization)),
            'created_at' => $createdAt instanceof DateTimeInterface ? $createdAt->format(DateTimeInterface::ATOM) : null,
        ];
    }
}
