<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Throwable;

abstract class ApiController extends Controller
{
    protected function success(mixed $data, int $status = 200, array $meta = []): JsonResponse
    {
        $payload = ['data' => $data];

        if (! empty($meta)) {
            $payload['meta'] = $meta;
        }

        return response()->json($payload, $status);
    }

    protected function error(string $code, string $message, int $status = 400, array $details = []): JsonResponse
    {
        $payload = [
            'error' => [
                'code' => $code,
                'message' => $message,
            ],
        ];

        if (! empty($details)) {
            $payload['error']['details'] = $details;
        }

        return response()->json($payload, $status);
    }

    protected function errorFromException(Throwable $e, int $status = 500): JsonResponse
    {
        return $this->error(
            code: 'internal_error',
            message: $e->getMessage(),
            status: $status,
        );
    }
}
