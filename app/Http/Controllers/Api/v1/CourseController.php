<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Resources\Api\v1\CourseResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Campus\Models\Course;

class CourseController extends ApiController
{
    private const ALLOWED_INCLUDES = ['studyProgram'];

    public function index(Request $request): JsonResponse
    {
        $includes = $this->resolveIncludes($request, self::ALLOWED_INCLUDES);

        $query = Course::query()
            ->with($includes)
            ->orderBy('name');

        $this->applyFilters(
            $query, $request,
            searchFields: ['name', 'code'],
            booleanFields: ['is_active', 'is_mandatory'],
            exactFields: ['course_type', 'semester_level', 'study_program_id'],
        );

        return $this->collectionResponse($query, $request, CourseResource::class);
    }

    public function show(Request $request, Course $course): JsonResponse
    {
        $includes = $this->resolveIncludes($request, self::ALLOWED_INCLUDES);
        $this->abortIfCrossTenant($course);
        $course->load($includes);

        return $this->success(new CourseResource($course));
    }
}
