<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Requests\Api\V1\StoreLeaveRequestRequest;
use App\Support\CurrentTenant;
use Illuminate\Http\JsonResponse;
use Modules\Employee\Models\LeaveRequest;

class LeaveRequestController extends ApiController
{
    public function __construct(
        private readonly CurrentTenant $currentTenant,
    ) {}

    public function store(StoreLeaveRequestRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $leaveRequest = LeaveRequest::create(array_merge($validated, [
            'tenant_id' => $this->currentTenant->id(),
            'status' => 'draft',
        ]));

        return $this->success([
            'id' => $leaveRequest->id,
            'employee_id' => $leaveRequest->employee_id,
            'leave_type' => $leaveRequest->leave_type,
            'start_date' => $leaveRequest->start_date?->toDateString(),
            'end_date' => $leaveRequest->end_date?->toDateString(),
            'total_days' => $leaveRequest->total_days,
            'status' => $leaveRequest->status,
            'created_at' => $leaveRequest->created_at?->toIso8601String(),
        ], 201);
    }
}
