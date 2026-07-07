<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Requests\Api\V1\StoreApplicantRequest;
use App\Services\WebhookDispatcher;
use App\Support\CurrentTenant;
use Illuminate\Http\JsonResponse;
use Modules\Enrollment\Models\Applicant;

class ApplicantController extends ApiController
{
    public function __construct(
        private readonly WebhookDispatcher $webhooks,
        private readonly CurrentTenant $currentTenant,
    ) {}

    public function store(StoreApplicantRequest $request): JsonResponse
    {
        $tenantId = $this->currentTenant->id();
        $validated = $request->validated();

        $applicant = Applicant::create(array_merge($validated, [
            'tenant_id' => $tenantId,
            'status' => $validated['status'] ?? 'registered',
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
