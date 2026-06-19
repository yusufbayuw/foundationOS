<?php

namespace App\Integrations\Moodle;

use App\Integrations\Moodle\Exceptions\MoodleIntegrationException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class MoodleClient
{
    public function call(string $function, array $params = []): array
    {
        if (! config('moodle.enabled')) {
            throw new MoodleIntegrationException('Moodle sync is disabled. Set MOODLE_SYNC_ENABLED=true.');
        }

        $baseUrl = (string) config('moodle.base_url');
        $token = (string) config('moodle.token');

        if ($baseUrl === '' || $token === '') {
            throw new MoodleIntegrationException('Moodle configuration is incomplete. Set MOODLE_BASE_URL and MOODLE_WS_TOKEN.');
        }

        $endpoint = rtrim($baseUrl, '/').'/webservice/rest/server.php';
        $payload = array_merge($params, [
            'wstoken' => $token,
            'wsfunction' => $function,
            'moodlewsrestformat' => (string) config('moodle.wsformat', 'json'),
        ]);

        try {
            $response = Http::timeout((int) config('moodle.timeout', 15))
                ->withOptions([
                    'verify' => (bool) config('moodle.verify_ssl', true),
                ])
                ->asForm()
                ->post($endpoint, $payload);
        } catch (ConnectionException $exception) {
            throw new MoodleIntegrationException('Unable to connect to Moodle: '.$exception->getMessage(), 0, $exception);
        }

        if ($response->failed()) {
            throw new MoodleIntegrationException('Moodle HTTP request failed with status '.$response->status().'.');
        }

        $body = trim((string) $response->body());
        if ($body === '' || strtolower($body) === 'null') {
            return [];
        }

        $json = $response->json();

        if (! is_array($json)) {
            throw new MoodleIntegrationException('Unexpected Moodle response format (non-JSON array).');
        }

        if (array_key_exists('exception', $json)) {
            $message = (string) ($json['message'] ?? $json['errorcode'] ?? 'Unknown Moodle exception');
            $errorCode = isset($json['errorcode']) ? (string) $json['errorcode'] : '';
            $debugInfo = isset($json['debuginfo']) ? (string) $json['debuginfo'] : '';

            if ($errorCode !== '') {
                $message .= " [{$errorCode}]";
            }

            if ($debugInfo !== '') {
                $message .= " | {$debugInfo}";
            }

            throw new MoodleIntegrationException((string) $message);
        }

        if (array_key_exists('error', $json) && is_string($json['error']) && Str::length($json['error']) > 0) {
            throw new MoodleIntegrationException($json['error']);
        }

        return $json;
    }
}
