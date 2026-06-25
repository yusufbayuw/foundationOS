<?php

namespace App\Http\Controllers\Api\v1;

use App\Services\WebhookDispatcher;
use App\Support\CurrentTenant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Modules\Enrollment\Models\Applicant;

class ApplicantController extends ApiController
{
    public function __construct(
        private readonly WebhookDispatcher $webhooks,
        private readonly CurrentTenant $currentTenant,
    ) {}

    public function store(Request $request): JsonResponse
    {
        $tenantId = $this->currentTenant->id();

        $validator = Validator::make($request->all(), [
            'admission_period_id' => [
                'required',
                'integer',
                Rule::exists('admission_periods', 'id')->where('tenant_id', $tenantId),
            ],
            'registration_number' => ['required', 'string', 'max:50'],
            'full_name' => ['required', 'string', 'max:255'],
            'birth_date' => ['nullable', 'date'],
            'gender' => ['nullable', 'in:male,female'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email'],
            'nisn' => ['nullable', 'string', 'max:20'],
            'previous_school' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return $this->error('validation_failed', 'The given data was invalid.', 422, $validator->errors()->toArray());
        }

        $applicant = Applicant::create(array_merge($validator->validated(), [
            'tenant_id' => $tenantId,
            'status' => $validator->validated()['status'] ?? 'registered',
            'achievement_count' => 0,
        ]));

        if ($tenantId) {
            $this->webhooks->dispatch((int) $tenantId, 'enrollment.created', [
                'resource' => 'applicant',
                'id' => $applicant->id,
                'registration_number' => $applicant->registration_number,
                'full_name' => $applicant->full_name,
                'status' => $applicant->status,
            ]);
        }

        return $this->success([
            'id' => $applicant->id,
            'registration_number' => $applicant->registration_number,
            'full_name' => $applicant->full_name,
            'status' => $applicant->status,
            'created_at' => $applicant->created_at?->toIso8601String(),
        ], 201);
    }
}
