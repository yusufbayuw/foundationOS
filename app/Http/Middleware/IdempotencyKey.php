<?php

namespace App\Http\Middleware;

use App\Support\TypedValue;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class IdempotencyKey
{
    private const TTL_SECONDS = 86400; // 24 hours

    public function handle(Request $request, Closure $next): SymfonyResponse
    {
        $key = $request->header('Idempotency-Key');

        if (! $key || ! in_array($request->method(), ['POST', 'PUT', 'PATCH'], true)) {
            $response = $next($request);

            return $response instanceof SymfonyResponse ? $response : response('');
        }

        $cacheKey = $this->cacheKey($request, $key);
        $bodyHash = hash('sha256', $request->getContent());

        $cached = Cache::get($cacheKey);

        if (is_array($cached)) {
            $cachedBodyHash = TypedValue::string($cached['body_hash'] ?? '');

            if ($cachedBodyHash !== $bodyHash) {
                return response()->json([
                    'error' => [
                        'code' => 'idempotency_conflict',
                        'message' => 'A different request body was used with this Idempotency-Key.',
                    ],
                ], 409);
            }

            return response(
                TypedValue::string($cached['body'] ?? ''),
                TypedValue::int($cached['status'] ?? 200),
            )
                ->header('Content-Type', 'application/json')
                ->header('X-Idempotency-Replayed', 'true');
        }

        $response = $next($request);

        if (! $response instanceof SymfonyResponse) {
            return response('');
        }

        if ($response->getStatusCode() < 500) {
            Cache::put($cacheKey, [
                'body' => $response->getContent(),
                'status' => $response->getStatusCode(),
                'body_hash' => $bodyHash,
            ], self::TTL_SECONDS);
        }

        return $response;
    }

    private function cacheKey(Request $request, string $key): string
    {
        $userId = TypedValue::string($request->user()?->getKey() ?? 'anon');
        $route = $request->path();

        return "idempotency:{$userId}:{$route}:".hash('sha256', $key);
    }
}
