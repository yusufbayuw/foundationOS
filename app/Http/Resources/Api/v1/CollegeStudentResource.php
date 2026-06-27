<?php

namespace App\Http\Resources\Api\v1;

use DateTimeInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Campus\Models\CollageStudent;

class CollegeStudentResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var CollageStudent $student */
        $student = $this->resource;
        $graduationDate = $student->getAttribute('graduation_date');
        $createdAt = $student->getAttribute('created_at');

        return [
            'id' => $student->getKey(),
            'student_number' => $student->getAttribute('student_number'),
            'national_student_number' => $student->getAttribute('national_student_number'),
            'full_name' => $student->getAttribute('full_name'),
            'email' => $student->getAttribute('email'),
            'phone' => $student->getAttribute('phone'),
            'status' => $student->getAttribute('status'),
            'entry_year' => $student->getAttribute('entry_year'),
            'entry_semester' => $student->getAttribute('entry_semester'),
            'current_semester' => $student->getAttribute('current_semester'),
            'admission_type' => $student->getAttribute('admission_type'),
            'graduation_date' => $graduationDate instanceof DateTimeInterface ? $graduationDate->format('Y-m-d') : null,
            'study_program_id' => $student->getAttribute('study_program_id'),
            'organization_id' => $student->getAttribute('organization_id'),
            'user_id' => $student->getAttribute('user_id'),
            'organization' => $this->whenLoaded('organization', fn () => new OrganizationResource($student->organization)),
            'user' => $this->whenLoaded('user', fn () => new UserResource($student->user)),
            'created_at' => $createdAt instanceof DateTimeInterface ? $createdAt->format(DateTimeInterface::ATOM) : null,
        ];
    }
}
