<?php

namespace Modules\Messaging\Services\Providers;

use Illuminate\Support\Facades\Http;
use Modules\Messaging\Contracts\PushNotificationProvider;
use Modules\Messaging\DTO\PushNotificationResult;

class FcmPushNotificationProvider implements PushNotificationProvider
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function send(string $token, string $title, string $body, array $data = []): PushNotificationResult
    {
        $serverKey = (string) config('messaging.push.fcm.server_key');

        if ($serverKey === '') {
            return new PushNotificationResult(
                successful: false,
                errorMessage: 'FCM server key is not configured.',
            );
        }

        $response = Http::timeout(10)
            ->connectTimeout(5)
            ->withToken($serverKey)
            ->post((string) config('messaging.push.fcm.endpoint'), [
                'to' => $token,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                ],
                'data' => $data,
            ]);

        $payload = $response->json();
        $error = data_get($payload, 'results.0.error');

        return new PushNotificationResult(
            successful: $response->successful() && (int) data_get($payload, 'success', 0) > 0,
            invalidToken: in_array($error, ['InvalidRegistration', 'NotRegistered'], true),
            providerMessageId: data_get($payload, 'results.0.message_id'),
            errorMessage: $error ?? ($response->successful() ? null : $response->body()),
        );
    }
}
