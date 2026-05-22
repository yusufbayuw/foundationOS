<?php

namespace App\Http\Resources\Api\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'credits' => $this->credits,
            'theory_credits' => $this->theory_credits,
            'practicum_credits' => $this->practicum_credits,
            'semester_level' => $this->semester_level,
            'course_type' => $this->course_type,
            'is_mandatory' => $this->is_mandatory,
            'is_active' => $this->is_active,
            'description' => $this->description,
            'study_program_id' => $this->study_program_id,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
