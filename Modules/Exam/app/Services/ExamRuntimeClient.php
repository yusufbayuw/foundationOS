<?php

namespace Modules\Exam\Services;

use App\Support\TypedValue;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Modules\Exam\Exceptions\ExamRuntimeException;

class ExamRuntimeClient
{
    public function isConfigured(): bool
    {
        $baseUrl = TypedValue::string(config('exam.runtime.base_url'));
        $apiKey = TypedValue::string(config('exam.runtime.api_key'));

        return $baseUrl !== '' && $apiKey !== '';
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function publishExam(array $payload): array
    {
        $runtimeId = TypedValue::string($payload['runtime_id'] ?? '');
        $foundationId = TypedValue::string($payload['foundation_id'] ?? '');

        return $this->request(
            method: $runtimeId === '' ? 'post' : 'put',
            path: $runtimeId === ''
                ? '/api/v1/integration/exams'
                : '/api/v1/integration/exams/'.$runtimeId,
            payload: $payload,
            idempotencyKey: 'publish:'.$foundationId,
        );
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function syncParticipants(array $payload): array
    {
        $runtimeId = TypedValue::string($payload['runtime_id'] ?? '');
        $foundationId = TypedValue::string($payload['foundation_id'] ?? '');

        if ($runtimeId === '') {
            throw new ExamRuntimeException('Runtime exam id is required before syncing participants.');
        }

        return $this->request(
            method: 'post',
            path: '/api/v1/integration/exams/'.$runtimeId.'/participants',
            payload: $payload,
            idempotencyKey: 'participants:'.$foundationId.':'.count($this->normalizeList($payload['participants'] ?? [])),
        );
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function syncAdminAccess(array $payload): array
    {
        $runtimeId = TypedValue::string($payload['runtime_id'] ?? '');
        $foundationId = TypedValue::string($payload['foundation_id'] ?? '');

        if ($runtimeId === '') {
            throw new ExamRuntimeException('Runtime exam id is required before syncing admin access.');
        }

        return $this->request(
            method: 'post',
            path: '/api/v1/integration/exams/'.$runtimeId.'/admin-access',
            payload: $payload,
            idempotencyKey: 'admin:'.$foundationId.':'.count($this->normalizeList($payload['admin_access'] ?? [])),
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

        $baseUrl = rtrim(TypedValue::string(config('exam.runtime.base_url')), '/');
        $url = $baseUrl.$path;

        $headers = [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer '.TypedValue::string(config('exam.runtime.api_key')),
            'X-FOS-Idempotency-Key' => $idempotencyKey,
        ];

        try {
            $response = Http::timeout(TypedValue::int(config('exam.runtime.timeout'), 30))
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

        $baseUrl = rtrim(TypedValue::string(config('exam.runtime.base_url')), '/');
        $url = $baseUrl.$path;
        $body = json_encode($payload, JSON_THROW_ON_ERROR);

        $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '.TypedValue::string(config('exam.runtime.api_key')),
            'X-FOS-Idempotency-Key' => $idempotencyKey,
        ];

        if (filled(config('exam.runtime.hmac_secret'))) {
            $headers['X-FOS-Signature'] = hash_hmac('sha256', $body, TypedValue::string(config('exam.runtime.hmac_secret')));
        }

        if (isset($payload['tenant']) && is_array($payload['tenant']) && isset($payload['tenant']['uuid']) && is_string($payload['tenant']['uuid'])) {
            $headers['X-FOS-Tenant-UUID'] = $payload['tenant']['uuid'];
        }

        try {
            $client = Http::timeout(TypedValue::int(config('exam.runtime.timeout'), 30))
                ->withHeaders($headers)
                ->withBody($body, 'application/json');
            $response = match (strtolower($method)) {
                'post' => $client->post($url),
                'put' => $client->put($url),
                'patch' => $client->patch($url),
                default => throw new ExamRuntimeException('Unsupported exam runtime request method: '.$method),
            };
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

        $json = $this->toStringKeyedArray($json);

        if ($response->failed()) {
            $message = TypedValue::string($json['message'] ?? $json['error'] ?? 'Exam runtime request failed.');
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
        $nestedData = isset($response['data']) && is_array($response['data']) ? $response['data'] : [];
        $runtimeId = TypedValue::string($response['runtime_id'] ?? $nestedData['runtime_id'] ?? '');

        if ($runtimeId === '' || ! Str::isUuid($runtimeId)) {
            throw new ExamRuntimeException('Exam runtime response did not include a valid runtime_id UUID.');
        }

        return [
            'runtime_exam_id' => $runtimeId,
            'publish_status' => TypedValue::string($response['status'] ?? $response['publish_status'] ?? 'published'),
            'external_id' => isset($response['external_id']) ? TypedValue::string($response['external_id']) : null,
        ];
    }

    /**
     * @return list<mixed>
     */
    private function normalizeList(mixed $value): array
    {
        if (! is_array($value)) {
            return [];
        }

        return array_is_list($value) ? $value : [$value];
    }

    /**
     * @param  array<mixed, mixed>  $value
     * @return array<string, mixed>
     */
    private function toStringKeyedArray(array $value): array
    {
        $result = [];

        foreach ($value as $key => $item) {
            $result[TypedValue::string($key)] = $item;
        }

        return $result;
    }
}
