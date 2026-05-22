<?php

namespace App\Http\Controllers\Api\v1;

use App\Support\CurrentTenant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Modules\Employee\Models\LeaveRequest;

class LeaveRequestController extends ApiController
{
    public function __construct(
        private readonly CurrentTenant $currentTenant,
    ) {}

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => ['required', 'integer'],
            'leave_type' => ['required', 'string', 'max:50'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'total_days' => ['required', 'integer', 'min:1'],
            'reason' => ['required', 'string'],
            'substitute_employee_id' => ['nullable', 'integer'],
        ]);

        if ($validator->fails()) {
            return $this->error('validation_failed', 'The given data was invalid.', 422, $validator->errors()->toArray());
        }

        $leaveRequest = LeaveRequest::create(array_merge($validator->validated(), [
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
