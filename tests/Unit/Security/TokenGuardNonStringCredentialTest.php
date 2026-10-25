<?php

namespace Tests\Unit\Security;

use Illuminate\Auth\TokenGuard;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\Request;
use Mockery;
use PHPUnit\Framework\TestCase;

class TokenGuardNonStringCredentialTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    /**
     * Preserve all malformed/valid-token regressions from security PR #123,
     * consolidated into one PHPUnit case without losing input variations.
     */
    public function test_token_guard_rejects_untrusted_credentials_without_blocking_valid_tokens(): void
    {
        $invalid = [
            'numeric' => 123,
            'boolean' => true,
            'array' => ['untrusted'],
            'associative array' => ['id' => 1],
            'nested array' => [['id' => 1]],
            'empty token' => '',
        ];

        foreach ($invalid as $label => $token) {
            $provider = Mockery::mock(UserProvider::class);
            $provider->shouldNotReceive('retrieveByCredentials');

            $request = Request::create('/api/protected', 'POST', ['api_token' => $token]);
            $guard = new TokenGuard($provider, $request);

            $this->assertNull($guard->user(), "Invalid {$label} credentials must not authenticate.");
            $this->assertFalse($guard->validate(['api_token' => $token]), "Invalid {$label} credentials must not validate.");
        }

        $queryProvider = Mockery::mock(UserProvider::class);
        $queryProvider->shouldNotReceive('retrieveByCredentials');
        $this->assertNull((new TokenGuard(
            $queryProvider,
            Request::create('/api/protected?api_token%5B%5D=1', 'GET'),
        ))->user(), 'Array token from query parameters must be rejected.');

        $emptyBearerProvider = Mockery::mock(UserProvider::class);
        $emptyBearerProvider->shouldNotReceive('retrieveByCredentials');
        $this->assertNull((new TokenGuard(
            $emptyBearerProvider,
            Request::create('/api/protected', 'GET', [], [], [], ['HTTP_AUTHORIZATION' => 'Bearer ']),
        ))->user(), 'Empty bearer token must not authenticate.');

        $goodProvider = Mockery::mock(UserProvider::class);
        $user = Mockery::mock(Authenticatable::class);
        $goodProvider->shouldReceive('retrieveByCredentials')
            ->once()
            ->with(['api_token' => 'known-good-token'])
            ->andReturn($user);

        $this->assertSame($user, (new TokenGuard(
            $goodProvider,
            Request::create('/api/protected', 'POST', ['api_token' => 'known-good-token']),
        ))->user(), 'Valid string token must authenticate as expected.');
    }
}
