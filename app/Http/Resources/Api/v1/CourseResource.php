<?php

namespace App\Http\Resources\Api\v1;

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

        return [
            'id' => $course->id,
            'code' => $course->code,
            'name' => $course->name,
            'credits' => $course->credits,
            'theory_credits' => $course->theory_credits,
            'practicum_credits' => $course->practicum_credits,
            'semester_level' => $course->semester_level,
            'course_type' => $course->course_type,
            'is_mandatory' => $course->is_mandatory,
            'is_active' => $course->is_active,
            'description' => $course->description,
            'study_program_id' => $course->study_program_id,
            'created_at' => $course->created_at?->toIso8601String(),
        ];
    }
}
