<?php

namespace Modules\Messaging\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;

class WhatsAppWebhookController extends Controller
{
    public function handle(Request $request, string $provider): JsonResponse
    {
        $secret = $this->secretFor($provider);

        if ($secret !== '') {
            $expected = 'sha256='.hash_hmac('sha256', $request->getContent(), $secret);
            $provided = (string) $request->header('X-Hub-Signature-256', '');

            abort_unless(hash_equals($expected, $provided), 403);
        }

        $webhookId = (string) ($request->header('X-Webhook-Id') ?: $request->input('id', ''));
        $idempotencyKey = $webhookId !== '' ? "whatsapp:webhook:{$provider}:{$webhookId}" : null;
        $duplicate = $idempotencyKey !== null && ! Cache::add($idempotencyKey, true, now()->addDay());

        return response()->json([
            'received' => true,
            'provider' => $provider,
            'verified' => $secret !== '',
            'duplicate' => $duplicate,
        ]);
    }

    protected function secretFor(string $provider): string
    {
        return (string) (config("messaging.webhooks.whatsapp.providers.{$provider}.secret")
            ?? config("messaging.webhooks.whatsapp.providers.{$provider}")
            ?? config('messaging.webhooks.whatsapp_secret', ''));
    }
}
