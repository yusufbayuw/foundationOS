<?php

namespace App\Http\Resources\Api\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Campus\Models\CollageStudent;

class CollegeStudentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var CollageStudent $student */
        $student = $this->resource;

        return [
            'id' => $student->id,
            'student_number' => $student->student_number,
            'national_student_number' => $student->national_student_number,
            'full_name' => $student->full_name,
            'email' => $student->email,
            'phone' => $student->phone,
            'status' => $student->status,
            'entry_year' => $student->entry_year,
            'entry_semester' => $student->entry_semester,
            'current_semester' => $student->current_semester,
            'admission_type' => $student->admission_type,
            'graduation_date' => $student->graduation_date?->toDateString(),
            'study_program_id' => $student->study_program_id,
            'organization_id' => $student->organization_id,
            'user_id' => $student->user_id,
            'organization' => $this->whenLoaded('organization', fn () => new OrganizationResource($student->organization)),
            'user' => $this->whenLoaded('user', fn () => new UserResource($student->user)),
            'created_at' => $student->created_at?->toIso8601String(),
        ];
    }
}
