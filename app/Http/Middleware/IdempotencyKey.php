<?php

namespace App\Http\Middleware;

use App\Support\CurrentTenant;
use Closure;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class IdempotencyKey
{
    private const LOCK_SECONDS = 120;

    private const LOCK_WAIT_SECONDS = 1;

    private const TTL_SECONDS = 86400;

    public function __construct(private CurrentTenant $currentTenant) {}

    public function handle(Request $request, Closure $next): SymfonyResponse
    {
        $key = $request->header('Idempotency-Key');

        if (! $key || ! in_array($request->method(), ['POST', 'PUT', 'PATCH'], true)) {
            return $next($request);
        }

        $cacheKey = $this->cacheKey($request, $key);
        $bodyHash = hash('sha256', implode("\n", [
            $request->method(),
            (string) $request->getQueryString(),
            $request->getContent(),
        ]));

        try {
            return Cache::lock("{$cacheKey}:lock", self::LOCK_SECONDS)
                ->block(self::LOCK_WAIT_SECONDS, function () use ($bodyHash, $cacheKey, $next, $request): SymfonyResponse {
                    $cachedResponse = $this->cachedResponse($cacheKey, $bodyHash);

                    if ($cachedResponse instanceof SymfonyResponse) {
                        return $cachedResponse;
                    }

                    $response = $next($request);

                    if ($response->getStatusCode() < 500) {
                        Cache::put($cacheKey, [
                            'body' => $response->getContent(),
                            'status' => $response->getStatusCode(),
                            'body_hash' => $bodyHash,
                        ], self::TTL_SECONDS);
                    }

                    return $response;
                });
        } catch (LockTimeoutException) {
            return response()->json([
                'error' => [
                    'code' => 'idempotency_in_progress',
                    'message' => 'A request with this Idempotency-Key is already being processed.',
                ],
            ], 409)->header('Retry-After', (string) self::LOCK_WAIT_SECONDS);
        }
    }

    private function cacheKey(Request $request, string $key): string
    {
        $tenantId = $this->currentTenant->id() ?? 'global';
        $userId = $request->user()?->getKey() ?? 'anon';
        $route = $request->method().':'.$request->path();

        return "idempotency:tenant:{$tenantId}:user:{$userId}:{$route}:".hash('sha256', $key);
    }

    private function cachedResponse(string $cacheKey, string $bodyHash): ?SymfonyResponse
    {
        /** @var array{body: string, status: int, body_hash: string}|null $cached */
        $cached = Cache::get($cacheKey);

        if ($cached === null) {
            return null;
        }

        if ($cached['body_hash'] !== $bodyHash) {
            return response()->json([
                'error' => [
                    'code' => 'idempotency_conflict',
                    'message' => 'A different request body was used with this Idempotency-Key.',
                ],
            ], 409);
        }

        return response($cached['body'], $cached['status'])
            ->header('Content-Type', 'application/json')
            ->header('X-Idempotency-Replayed', 'true');
    }
}
