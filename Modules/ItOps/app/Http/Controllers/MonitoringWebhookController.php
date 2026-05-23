<?php

namespace Modules\ItOps\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\ItOps\Contracts\MonitoringWebhookReceiver;

class MonitoringWebhookController extends Controller
{
    public function __invoke(Request $request, MonitoringWebhookReceiver $receiver): JsonResponse
    {
        $receiver->receive($request->all());

        return response()->json(['accepted' => true]);
    }
}
