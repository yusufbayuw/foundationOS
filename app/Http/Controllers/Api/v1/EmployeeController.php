<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Resources\Api\v1\EmployeeResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Employee\Models\Employee;

class EmployeeController extends ApiController
{
    private const ALLOWED_INCLUDES = ['organization', 'department', 'position'];

    public function index(Request $request): JsonResponse
    {
        $includes = $this->resolveIncludes($request, self::ALLOWED_INCLUDES);

        $query = Employee::query()
            ->with($includes)
            ->orderBy('full_name');

        $this->applyFilters(
            $query, $request,
            searchFields: ['full_name', 'employee_number', 'email'],
            booleanFields: [],
            exactFields: ['employment_type', 'employment_status', 'gender', 'organization_id', 'department_id', 'position_id'],
        );

        return $this->collectionResponse($query, $request, EmployeeResource::class);
    }

    public function show(Request $request, Employee $employee): JsonResponse
    {
        $includes = $this->resolveIncludes($request, self::ALLOWED_INCLUDES);
        $this->abortIfCrossTenant($employee);
        $employee->load($includes);

        return $this->success(new EmployeeResource($employee));
    }
}
