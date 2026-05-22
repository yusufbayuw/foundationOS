<?php

namespace App\Http\Resources\Api\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'employee_number' => $this->employee_number,
            'full_name' => $this->full_name,
            'gender' => $this->gender,
            'email' => $this->email,
            'phone' => $this->phone,
            'employment_type' => $this->employment_type,
            'employment_status' => $this->employment_status,
            'join_date' => $this->join_date?->toDateString(),
            'end_date' => $this->end_date?->toDateString(),
            'organization_id' => $this->organization_id,
            'department_id' => $this->department_id,
            'position_id' => $this->position_id,
            'user_id' => $this->user_id,
            'organization' => $this->whenLoaded('organization', fn () => new OrganizationResource($this->organization)),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
