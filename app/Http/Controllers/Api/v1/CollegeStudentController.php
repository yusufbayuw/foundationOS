<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Resources\Api\v1\CollegeStudentResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Campus\Models\CollageStudent;

class CollegeStudentController extends ApiController
{
    private const ALLOWED_INCLUDES = ['organization', 'user', 'studyProgram'];

    public function index(Request $request): JsonResponse
    {
        $includes = $this->resolveIncludes($request, self::ALLOWED_INCLUDES);

        $query = CollageStudent::query()
            ->with($includes)
            ->orderBy('id');

        $this->applyFilters(
            $query, $request,
            searchFields: ['student_number', 'national_student_number', 'full_name', 'email'],
            booleanFields: [],
            exactFields: ['status', 'entry_year', 'study_program_id', 'organization_id'],
        );

        return $this->collectionResponse($query, $request, CollegeStudentResource::class);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $includes = $this->resolveIncludes($request, self::ALLOWED_INCLUDES);
        $student = CollageStudent::with($includes)->findOrFail($id);

        return $this->success(new CollegeStudentResource($student));
    }
}
