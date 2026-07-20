<?php

namespace Modules\Enrollment\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Core\Models\Tenant;
use Modules\Enrollment\Http\Requests\InquiryRequest;
use Modules\Enrollment\Services\LeadInquiryService;

class InquiryController extends Controller
{
    public function __construct(
        private readonly LeadInquiryService $leadInquiryService,
    ) {}

    public function store(InquiryRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $tenant = Tenant::query()->where('code', $validated['tenant_code'])->firstOrFail();

        $utm = array_filter([
            'utm_source' => $validated['utm_source'] ?? null,
            'utm_medium' => $validated['utm_medium'] ?? null,
            'utm_campaign' => $validated['utm_campaign'] ?? null,
        ]);

        $lead = $this->leadInquiryService->createFromInquiry(
            tenantId: (int) $tenant->getKey(),
            fullName: $validated['full_name'],
            email: $validated['email'] ?? null,
            phone: $validated['phone'] ?? null,
            sourceDetail: $validated['source_detail'] ?? null,
            utm: $utm,
        );

        return response()->json([
            'lead_id' => $lead->getKey(),
            'message' => 'Inquiry received',
        ], 201);
    }
}
