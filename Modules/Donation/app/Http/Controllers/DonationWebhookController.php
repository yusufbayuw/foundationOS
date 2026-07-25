<?php

namespace Modules\Donation\Http\Controllers;

use App\Services\Billing\MidtransWebhookException;
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
        try {
            $donation = $this->paymentService->handleWebhook($request->all());
        } catch (MidtransWebhookException) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid webhook notification.',
            ], 400);
        }

        return response()->json([
            'donation_id' => $donation->getKey(),
            'status' => $donation->payment_status,
        ]);
    }
}
