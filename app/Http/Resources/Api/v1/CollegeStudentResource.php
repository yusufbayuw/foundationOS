<?php

namespace App\Http\Resources\Api\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CollegeStudentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'student_number' => $this->student_number,
            'national_student_number' => $this->national_student_number,
            'full_name' => $this->full_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'status' => $this->status,
            'entry_year' => $this->entry_year,
            'entry_semester' => $this->entry_semester,
            'current_semester' => $this->current_semester,
            'admission_type' => $this->admission_type,
            'graduation_date' => $this->graduation_date?->toDateString(),
            'study_program_id' => $this->study_program_id,
            'organization_id' => $this->organization_id,
            'user_id' => $this->user_id,
            'organization' => $this->whenLoaded('organization', fn () => new OrganizationResource($this->organization)),
            'user' => $this->whenLoaded('user', fn () => new UserResource($this->user)),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
