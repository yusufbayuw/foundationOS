<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Resources\Api\v1\SchoolClassResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\School\Models\SchoolClass;

class SchoolClassController extends ApiController
{
    private const ALLOWED_INCLUDES = ['organization', 'academicPeriod', 'department'];

    public function index(Request $request): JsonResponse
    {
        $includes = $this->resolveIncludes($request, self::ALLOWED_INCLUDES);

        $query = SchoolClass::query()
            ->with($includes)
            ->orderBy('name');

        $this->applyFilters(
            $query, $request,
            searchFields: ['name', 'code'],
            booleanFields: ['is_active'],
            exactFields: ['grade_level', 'organization_id', 'academic_period_id', 'department_id'],
        );

        return $this->collectionResponse($query, $request, SchoolClassResource::class);
    }

    public function show(Request $request, SchoolClass $class): JsonResponse
    {
        $includes = $this->resolveIncludes($request, self::ALLOWED_INCLUDES);
        $this->abortIfCrossTenant($class);
        $class->load($includes);

        return $this->success(new SchoolClassResource($class));
    }
}
