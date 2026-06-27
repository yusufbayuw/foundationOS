<?php

namespace App\Http\Controllers;

use App\Services\Billing\MidtransWebhookException;
use App\Services\BillingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Throwable;

class BillingController extends Controller
{
    public function __construct(private readonly BillingService $billingService) {}

    public function webhook(Request $request): JsonResponse
    {
        try {
            $payload = [];
            foreach ($request->all() as $key => $value) {
                $payload[(string) $key] = $value;
            }

            $this->billingService->handleWebhookNotification($payload);
        } catch (MidtransWebhookException $e) {
            report($e);

            return response()->json([
                'status' => 'error',
                'message' => 'Invalid webhook notification.',
            ], 400);
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'status' => 'error',
                'message' => 'Unable to process webhook notification.',
            ], 500);
        }

        return response()->json(['status' => 'ok']);
    }

    public function finish(Request $request, int $tenant): RedirectResponse
    {
        return redirect('/admin');
    }
}
