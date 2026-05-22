<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Resources\Api\v1\StudentResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\School\Models\Student;

class StudentController extends ApiController
{
    private const ALLOWED_INCLUDES = ['organization', 'user', 'academicYear'];

    public function index(Request $request): JsonResponse
    {
        $includes = $this->resolveIncludes($request, self::ALLOWED_INCLUDES);

        $query = Student::query()
            ->with($includes)
            ->orderBy('id');

        $this->applyFilters(
            $query, $request,
            searchFields: ['nis', 'nisn'],
            booleanFields: [],
            exactFields: ['status', 'track', 'organization_id', 'academic_year_id'],
        );

        return $this->collectionResponse($query, $request, StudentResource::class);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $includes = $this->resolveIncludes($request, self::ALLOWED_INCLUDES);
        $student = Student::with($includes)->findOrFail($id);

        return $this->success(new StudentResource($student));
    }
}
