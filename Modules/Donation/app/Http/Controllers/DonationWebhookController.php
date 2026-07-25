<?php

namespace Modules\Donation\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Donation\Http\Requests\DonationWebhookRequest;
use Modules\Donation\Services\DonationPaymentService;

class DonationWebhookController extends Controller
{
    public function __construct(
        private readonly DonationPaymentService $paymentService,
    ) {}

    public function handle(DonationWebhookRequest $request): JsonResponse
    {
        $donation = $this->paymentService->handleWebhook($request->validated());

        return response()->json([
            'donation_id' => $donation->getKey(),
            'status' => $donation->payment_status,
        ]);
    }
}
