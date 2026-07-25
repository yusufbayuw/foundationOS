<?php

namespace Modules\Donation\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Donation\Services\DonationCheckoutService;
use Throwable;

class DonationCheckoutController extends Controller
{
    public function __construct(
        private readonly DonationCheckoutService $checkoutService,
    ) {}

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'tenant_id' => ['required', 'integer', 'exists:tenants,id'],
            'campaign_id' => ['required', 'integer', 'exists:campaigns,id'],
            'amount' => ['required', 'numeric', 'min:1'],
            'donor.name' => ['nullable', 'string', 'max:255'],
            'donor.email' => ['nullable', 'email', 'max:255'],
            'donor.phone' => ['nullable', 'string', 'max:50'],
            'donor.is_anonymous' => ['nullable', 'boolean'],
            'options' => ['nullable', 'array'],
        ]);

        try {
            $checkout = $this->checkoutService->checkout($validated);
        } catch (Throwable) {
            return response()->json([
                'message' => 'Unable to create donation payment transaction.',
            ], 502);
        }

        return response()->json([
            'donation_id' => $checkout['donation']->getKey(),
            'donation_number' => $checkout['donation']->donation_number,
            'payment_status' => $checkout['donation']->payment_status,
            'payment_reference' => $checkout['donation']->payment_reference,
            'token' => $checkout['gateway']['token'] ?? null,
            'redirect_url' => $checkout['gateway']['redirect_url'] ?? null,
        ], 201);
    }
}
