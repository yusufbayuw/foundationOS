<?php

namespace Modules\Messaging\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class WhatsAppWebhookController extends Controller
{
    public function handle(Request $request, string $provider): JsonResponse
    {
        $verified = $this->verifyProviderSignature($request);

        return response()->json([
            'received' => true,
            'provider' => $provider,
            'verified' => $verified,
        ]);
    }

    private function verifyProviderSignature(Request $request): bool
    {
        $secret = (string) config('messaging.webhooks.whatsapp_secret', '');

        if ($secret === '') {
            return false;
        }

        $expected = 'sha256='.hash_hmac('sha256', $request->getContent(), $secret);
        $provided = (string) $request->header('X-Hub-Signature-256', '');

        abort_unless(hash_equals($expected, $provided), 403);

        return true;
    }
}
