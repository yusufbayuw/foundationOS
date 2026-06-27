<?php

namespace App\Http\Resources\Api\v1;

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

        return [
            'id' => $student->id,
            'nis' => $student->nis,
            'nisn' => $student->nisn,
            'status' => $student->status,
            'track' => $student->track,
            'entry_date' => $student->entry_date?->toDateString(),
            'entry_type' => $student->entry_type,
            'graduation_date' => $student->graduation_date?->toDateString(),
            'organization_id' => $student->organization_id,
            'academic_year_id' => $student->academic_year_id,
            'user_id' => $student->user_id,
            'organization' => $this->whenLoaded('organization', fn () => new OrganizationResource($student->organization)),
            'user' => $this->whenLoaded('user', fn () => new UserResource($student->user)),
            'created_at' => $student->created_at?->toIso8601String(),
        ];
    }
}
