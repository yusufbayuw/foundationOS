<?php

namespace Modules\Enrollment\Http\Controllers;

use App\Support\TypedValue;
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
        /** @var array{
         *     tenant_code: string,
         *     full_name: string,
         *     email?: string|null,
         *     phone?: string|null,
         *     source_detail?: string|null,
         *     utm_source?: string|null,
         *     utm_medium?: string|null,
         *     utm_campaign?: string|null
         * } $validated
         */
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

        $tenantCode = TypedValue::string($validated['tenant_code']);
        $tenant = Tenant::query()->where('code', $tenantCode)->firstOrFail();

        $utm = array_filter([
            'utm_source' => $validated['utm_source'] ?? null,
            'utm_medium' => $validated['utm_medium'] ?? null,
            'utm_campaign' => $validated['utm_campaign'] ?? null,
        ]);

        $lead = $this->leadInquiryService->createFromInquiry(
            tenantId: TypedValue::int($tenant->getKey()),
            fullName: TypedValue::string($validated['full_name']),
            email: TypedValue::string($validated['email'] ?? '') ?: null,
            phone: TypedValue::string($validated['phone'] ?? '') ?: null,
            sourceDetail: TypedValue::string($validated['source_detail'] ?? '') ?: null,
            utm: $utm,
        );

        return response()->json([
            'lead_id' => $lead->getKey(),
            'message' => 'Inquiry received',
        ], 201);
    }
}
