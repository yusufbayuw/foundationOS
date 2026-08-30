<?php

namespace Tests\Feature;

use App\Http\Middleware\IdempotencyKey;
use App\Support\CurrentTenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Modules\Core\Models\User;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class IdempotencyKeyIsolationTest extends TestCase
{
    protected function tearDown(): void
    {
        Cache::flush();
        app(CurrentTenant::class)->forget();

        parent::tearDown();
    }

    public function test_same_user_and_key_are_isolated_between_tenants(): void
    {
        $currentTenant = app(CurrentTenant::class);
        $middleware = new IdempotencyKey($currentTenant);
        $user = $this->user(77);
        $executions = 0;

        $currentTenant->set(101);
        $first = $middleware->handle(
            $this->request($user, 'shared-key'),
            function () use (&$executions, $currentTenant): Response {
                $executions++;

                return response()->json(['tenant_id' => $currentTenant->id()], 201);
            },
        );

        $currentTenant->set(202);
        $second = $middleware->handle(
            $this->request($user, 'shared-key'),
            function () use (&$executions, $currentTenant): Response {
                $executions++;

                return response()->json(['tenant_id' => $currentTenant->id()], 201);
            },
        );

        $this->assertSame(2, $executions);
        $this->assertSame(101, $this->responseData($first)['tenant_id']);
        $this->assertSame(202, $this->responseData($second)['tenant_id']);
        $this->assertFalse($second->headers->has('X-Idempotency-Replayed'));
    }

    public function test_same_tenant_user_key_and_body_replays_the_first_response(): void
    {
        $currentTenant = app(CurrentTenant::class);
        $currentTenant->set(303);
        $middleware = new IdempotencyKey($currentTenant);
        $user = $this->user(88);
        $executions = 0;

        $next = function () use (&$executions): Response {
            $executions++;

            return response()->json(['execution' => $executions], 201);
        };

        $first = $middleware->handle($this->request($user, 'replay-key'), $next);
        $second = $middleware->handle($this->request($user, 'replay-key'), $next);

        $this->assertSame(1, $executions);
        $this->assertSame($this->responseData($first), $this->responseData($second));
        $this->assertSame('true', $second->headers->get('X-Idempotency-Replayed'));
    }

    public function test_overlapping_request_is_rejected_while_the_first_request_holds_the_lock(): void
    {
        $currentTenant = app(CurrentTenant::class);
        $currentTenant->set(404);
        $middleware = new IdempotencyKey($currentTenant);
        $user = $this->user(99);
        $nestedResponse = null;
        $nestedExecuted = false;

        $outerResponse = $middleware->handle(
            $this->request($user, 'concurrent-key'),
            function () use (&$nestedExecuted, &$nestedResponse, $middleware, $user): Response {
                $nestedResponse = $middleware->handle(
                    $this->request($user, 'concurrent-key'),
                    function () use (&$nestedExecuted): Response {
                        $nestedExecuted = true;

                        return response()->json(['unexpected' => true], 201);
                    },
                );

                return response()->json(['created' => true], 201);
            },
        );

        $this->assertSame(201, $outerResponse->getStatusCode());
        $this->assertInstanceOf(Response::class, $nestedResponse);
        $this->assertSame(409, $nestedResponse->getStatusCode());
        $this->assertSame('idempotency_in_progress', $this->responseData($nestedResponse)['error']['code']);
        $this->assertFalse($nestedExecuted);
    }

    private function request(User $user, string $key): Request
    {
        $request = Request::create(
            '/api/v1/applicants',
            'POST',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: '{"name":"Same payload"}',
        );
        $request->headers->set('Idempotency-Key', $key);
        $request->setUserResolver(fn (): User => $user);

        return $request;
    }

    private function user(int $id): User
    {
        $user = new User;
        $user->setAttribute($user->getKeyName(), $id);

        return $user;
    }

    /**
     * @return array<string, mixed>
     */
    private function responseData(Response $response): array
    {
        return json_decode((string) $response->getContent(), true, flags: JSON_THROW_ON_ERROR);
    }
}
