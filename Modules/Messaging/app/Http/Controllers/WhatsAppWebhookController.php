<?php

namespace Modules\Messaging\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class WhatsAppWebhookController extends Controller
{
    public function handle(Request $request, string $provider): JsonResponse
    {
        // Provider-specific signature validation deferred to v09 integration sprint.
        return response()->json(['received' => true, 'provider' => $provider]);
    }
}
