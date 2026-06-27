<?php

namespace App\Http\Resources\Api\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\School\Models\SchoolClass;

class SchoolClassResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var SchoolClass $class */
        $class = $this->resource;

        return [
            'id' => $class->id,
            'name' => $class->name,
            'code' => $class->code,
            'grade_level' => $class->grade_level,
            'capacity' => $class->capacity,
            'student_count' => $class->student_count,
            'is_active' => $class->is_active,
            'description' => $class->description,
            'organization_id' => $class->organization_id,
            'academic_period_id' => $class->academic_period_id,
            'department_id' => $class->department_id,
            'organization' => $this->whenLoaded('organization', fn () => new OrganizationResource($class->organization)),
            'created_at' => $class->created_at?->toIso8601String(),
        ];
    }
}
