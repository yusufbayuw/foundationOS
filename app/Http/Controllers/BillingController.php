<?php

namespace App\Http\Controllers;

use App\Http\Requests\BillingWebhookRequest;
use App\Services\Billing\MidtransWebhookException;
use App\Services\BillingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Throwable;

class BillingController extends Controller
{
    public function __construct(private readonly BillingService $billingService) {}

    public function webhook(BillingWebhookRequest $request): JsonResponse
    {
        try {
            $this->billingService->handleWebhookNotification($request->validated());
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
