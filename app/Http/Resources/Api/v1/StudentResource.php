<?php

namespace App\Http\Resources\Api\v1;

use DateTimeInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\School\Models\Student;

class StudentResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var Student $student */
        $student = $this->resource;
        $entryDate = $student->getAttribute('entry_date');
        $graduationDate = $student->getAttribute('graduation_date');
        $createdAt = $student->getAttribute('created_at');

        return [
            'id' => $student->getKey(),
            'nis' => $student->getAttribute('nis'),
            'nisn' => $student->getAttribute('nisn'),
            'status' => $student->getAttribute('status'),
            'track' => $student->getAttribute('track'),
            'entry_date' => $entryDate instanceof DateTimeInterface ? $entryDate->format('Y-m-d') : null,
            'entry_type' => $student->getAttribute('entry_type'),
            'graduation_date' => $graduationDate instanceof DateTimeInterface ? $graduationDate->format('Y-m-d') : null,
            'organization_id' => $student->getAttribute('organization_id'),
            'academic_year_id' => $student->getAttribute('academic_year_id'),
            'user_id' => $student->getAttribute('user_id'),
            'organization' => $this->whenLoaded('organization', fn () => new OrganizationResource($student->organization)),
            'user' => $this->whenLoaded('user', fn () => new UserResource($student->user)),
            'created_at' => $createdAt instanceof DateTimeInterface ? $createdAt->format(DateTimeInterface::ATOM) : null,
        ];
    }
}
