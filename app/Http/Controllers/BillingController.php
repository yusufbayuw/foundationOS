<?php

namespace App\Http\Controllers;

use App\Services\BillingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Midtrans\Config as MidtransConfig;
use Midtrans\Notification;

class BillingController extends Controller
{
    public function __construct(private readonly BillingService $billingService) {}

    public function webhook(Request $request): JsonResponse
    {
        MidtransConfig::$serverKey = config('midtrans.server_key');
        MidtransConfig::$isProduction = config('midtrans.is_production');

        try {
            $notification = new Notification;
            $this->billingService->handleWebhookNotification((array) $notification);
        } catch (\Exception $e) {
            report($e);

            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }

        return response()->json(['status' => 'ok']);
    }

    public function finish(Request $request, int $tenant): RedirectResponse
    {
        return redirect('/admin');
    }
}
