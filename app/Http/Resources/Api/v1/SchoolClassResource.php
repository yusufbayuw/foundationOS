<?php

namespace App\Http\Resources\Api\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolClassResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'grade_level' => $this->grade_level,
            'capacity' => $this->capacity,
            'student_count' => $this->student_count,
            'is_active' => $this->is_active,
            'description' => $this->description,
            'organization_id' => $this->organization_id,
            'academic_period_id' => $this->academic_period_id,
            'department_id' => $this->department_id,
            'organization' => $this->whenLoaded('organization', fn () => new OrganizationResource($this->organization)),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
