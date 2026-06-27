<?php

namespace App\Http\Resources\Api\v1;

use DateTimeInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Campus\Models\Course;

class CourseResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var Course $course */
        $course = $this->resource;
        $createdAt = $course->getAttribute('created_at');

        return [
            'id' => $course->getKey(),
            'code' => $course->getAttribute('code'),
            'name' => $course->getAttribute('name'),
            'credits' => $course->getAttribute('credits'),
            'theory_credits' => $course->getAttribute('theory_credits'),
            'practicum_credits' => $course->getAttribute('practicum_credits'),
            'semester_level' => $course->getAttribute('semester_level'),
            'course_type' => $course->getAttribute('course_type'),
            'is_mandatory' => $course->getAttribute('is_mandatory'),
            'is_active' => $course->getAttribute('is_active'),
            'description' => $course->getAttribute('description'),
            'study_program_id' => $course->getAttribute('study_program_id'),
            'created_at' => $createdAt instanceof DateTimeInterface ? $createdAt->format(DateTimeInterface::ATOM) : null,
        ];
    }
}
