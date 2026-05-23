<?php

namespace Modules\Enrollment\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Models\Tenant;
use Modules\Enrollment\Services\LeadInquiryService;

class InquiryController extends Controller
{
    public function __construct(
        private readonly LeadInquiryService $leadInquiryService,
    ) {}

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'tenant_code' => ['required', 'string'],
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'source_detail' => ['nullable', 'string', 'max:255'],
            'utm_source' => ['nullable', 'string', 'max:100'],
            'utm_medium' => ['nullable', 'string', 'max:100'],
            'utm_campaign' => ['nullable', 'string', 'max:100'],
        ]);

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
