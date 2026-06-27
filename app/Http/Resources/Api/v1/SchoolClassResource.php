<?php

namespace App\Http\Resources\Api\v1;

use DateTimeInterface;
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
        $createdAt = $class->getAttribute('created_at');

        return [
            'id' => $class->getKey(),
            'name' => $class->getAttribute('name'),
            'code' => $class->getAttribute('code'),
            'grade_level' => $class->getAttribute('grade_level'),
            'capacity' => $class->getAttribute('capacity'),
            'student_count' => $class->getAttribute('student_count'),
            'is_active' => $class->getAttribute('is_active'),
            'description' => $class->getAttribute('description'),
            'organization_id' => $class->getAttribute('organization_id'),
            'academic_period_id' => $class->getAttribute('academic_period_id'),
            'department_id' => $class->getAttribute('department_id'),
            'organization' => $this->whenLoaded('organization', fn () => new OrganizationResource($class->organization)),
            'created_at' => $createdAt instanceof DateTimeInterface ? $createdAt->format(DateTimeInterface::ATOM) : null,
        ];
    }
}
