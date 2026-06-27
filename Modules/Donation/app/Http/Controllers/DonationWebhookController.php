<?php

namespace Modules\Donation\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Donation\Services\DonationPaymentService;

class DonationWebhookController extends Controller
{
    public function __construct(
        private readonly DonationPaymentService $paymentService,
    ) {}

    public function handle(Request $request): JsonResponse
    {
        $payload = [];
        foreach ($request->all() as $key => $value) {
            $payload[(string) $key] = $value;
        }

        $donation = $this->paymentService->handleWebhook($payload);

        return response()->json([
            'donation_id' => $donation->getKey(),
            'status' => $donation->payment_status,
        ]);
    }
}
