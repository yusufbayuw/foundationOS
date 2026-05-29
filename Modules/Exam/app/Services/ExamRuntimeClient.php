<?php

namespace Modules\Exam\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Modules\Exam\Exceptions\ExamRuntimeException;

class ExamRuntimeClient
{
    public function isConfigured(): bool
    {
        $baseUrl = (string) config('exam.runtime.base_url');
        $apiKey = (string) config('exam.runtime.api_key');

        return $baseUrl !== '' && $apiKey !== '';
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function publishExam(array $payload): array
    {
        return $this->request(
            method: empty($payload['runtime_id']) ? 'post' : 'put',
            path: empty($payload['runtime_id'])
                ? '/api/v1/integration/exams'
                : '/api/v1/integration/exams/'.($payload['runtime_id']),
            payload: $payload,
            idempotencyKey: 'publish:'.$payload['foundation_id'],
        );
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function syncParticipants(array $payload): array
    {
        $runtimeId = $payload['runtime_id'] ?? null;

        if ($runtimeId === null) {
            throw new ExamRuntimeException('Runtime exam id is required before syncing participants.');
        }

        return $this->request(
            method: 'post',
            path: '/api/v1/integration/exams/'.$runtimeId.'/participants',
            payload: $payload,
            idempotencyKey: 'participants:'.$payload['foundation_id'].':'.count($payload['participants'] ?? []),
        );
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function syncAdminAccess(array $payload): array
    {
        $runtimeId = $payload['runtime_id'] ?? null;

        if ($runtimeId === null) {
            throw new ExamRuntimeException('Runtime exam id is required before syncing admin access.');
        }

        return $this->request(
            method: 'post',
            path: '/api/v1/integration/exams/'.$runtimeId.'/admin-access',
            payload: $payload,
            idempotencyKey: 'admin:'.$payload['foundation_id'].':'.count($payload['admin_access'] ?? []),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function fetchExamResults(string $runtimeExamId): array
    {
        if (! Str::isUuid($runtimeExamId)) {
            throw new ExamRuntimeException('Runtime exam id must be a valid UUID.');
        }

        return $this->getRequest(
            path: '/api/integration/results/'.$runtimeExamId,
            idempotencyKey: 'results:'.$runtimeExamId,
        );
    }

    /**
     * @return array<string, mixed>
     */
    protected function getRequest(string $path, string $idempotencyKey): array
    {
        if (! $this->isConfigured()) {
            throw new ExamRuntimeException(
                'Exam runtime is not configured. Set EXAM_RUNTIME_BASE_URL and EXAM_RUNTIME_API_KEY.',
            );
        }

        $baseUrl = rtrim((string) config('exam.runtime.base_url'), '/');
        $url = $baseUrl.$path;

        $headers = [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer '.config('exam.runtime.api_key'),
            'X-FOS-Idempotency-Key' => $idempotencyKey,
        ];

        try {
            $response = Http::timeout((int) config('exam.runtime.timeout', 30))
                ->withHeaders($headers)
                ->get($url);
        } catch (ConnectionException $exception) {
            throw new ExamRuntimeException(
                'Unable to reach exam runtime: '.$exception->getMessage(),
                previous: $exception,
            );
        }

        return $this->decodeResponse($response);
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    protected function request(string $method, string $path, array $payload, string $idempotencyKey): array
    {
        if (! $this->isConfigured()) {
            throw new ExamRuntimeException(
                'Exam runtime is not configured. Set EXAM_RUNTIME_BASE_URL and EXAM_RUNTIME_API_KEY.',
            );
        }

        $baseUrl = rtrim((string) config('exam.runtime.base_url'), '/');
        $url = $baseUrl.$path;
        $body = json_encode($payload, JSON_THROW_ON_ERROR);

        $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '.config('exam.runtime.api_key'),
            'X-FOS-Idempotency-Key' => $idempotencyKey,
        ];

        if (filled(config('exam.runtime.hmac_secret'))) {
            $headers['X-FOS-Signature'] = hash_hmac('sha256', $body, (string) config('exam.runtime.hmac_secret'));
        }

        if (isset($payload['tenant']['uuid']) && is_string($payload['tenant']['uuid'])) {
            $headers['X-FOS-Tenant-UUID'] = $payload['tenant']['uuid'];
        }

        try {
            $response = Http::timeout((int) config('exam.runtime.timeout', 30))
                ->withHeaders($headers)
                ->withBody($body, 'application/json')
                ->{$method}($url);
        } catch (ConnectionException $exception) {
            throw new ExamRuntimeException(
                'Unable to reach exam runtime: '.$exception->getMessage(),
                previous: $exception,
            );
        }

        return $this->decodeResponse($response);
    }

    /**
     * @return array<string, mixed>
     */
    protected function decodeResponse(Response $response): array
    {
        $json = $response->json();

        if (! is_array($json)) {
            $json = ['message' => trim((string) $response->body())];
        }

        if (isset($json['data']) && is_array($json['data']) && ! isset($json['attempts'])) {
            $json = array_merge($json, $json['data']);
        }

        if ($response->failed()) {
            $message = (string) ($json['message'] ?? $json['error'] ?? 'Exam runtime request failed.');
            $details = $json['errors'] ?? $json['details'] ?? null;

            if (is_array($details)) {
                $message .= ' '.json_encode($details, JSON_UNESCAPED_UNICODE);
            }

            throw new ExamRuntimeException(
                $message.' (HTTP '.$response->status().')',
                $response->status(),
                $json,
            );
        }

        return $json;
    }

    /**
     * @param  array<string, mixed>  $response
     * @return array{runtime_exam_id: string, publish_status: string, external_id: ?string}
     */
    public function parsePublishResponse(array $response): array
    {
        $runtimeId = (string) ($response['runtime_id'] ?? $response['data']['runtime_id'] ?? '');

        if ($runtimeId === '' || ! Str::isUuid($runtimeId)) {
            throw new ExamRuntimeException('Exam runtime response did not include a valid runtime_id UUID.');
        }

        return [
            'runtime_exam_id' => $runtimeId,
            'publish_status' => (string) ($response['status'] ?? $response['publish_status'] ?? 'published'),
            'external_id' => isset($response['external_id']) ? (string) $response['external_id'] : null,
        ];
    }
}
