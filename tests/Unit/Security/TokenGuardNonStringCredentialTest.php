<?php

namespace Tests\Unit\Security;

use Illuminate\Auth\TokenGuard;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\Request;
use Mockery;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class TokenGuardNonStringCredentialTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    public static function invalidCredentials(): array
    {
        return [
            'numeric' => [123],
            'boolean' => [true],
            'array' => [['untrusted']],
            'associative array' => [['id' => 1]],
            'nested array' => [[['id' => 1]]],
            'empty token' => [''],
        ];
    }

    #[DataProvider('invalidCredentials')]
    public function test_token_guard_does_not_query_user_provider_with_non_string_or_empty_tokens(mixed $token): void
    {
        $provider = Mockery::mock(UserProvider::class);
        $provider->shouldNotReceive('retrieveByCredentials');

        $request = Request::create('/api/protected', 'POST', ['api_token' => $token]);
        $guard = new TokenGuard($provider, $request);

        $this->assertNull($guard->user());
        $this->assertFalse($guard->validate(['api_token' => $token]));
    }

    public function test_token_guard_rejects_an_array_from_query_parameters_without_user_lookup(): void
    {
        $provider = Mockery::mock(UserProvider::class);
        $provider->shouldNotReceive('retrieveByCredentials');

        $request = Request::create('/api/protected?api_token%5B%5D=1', 'GET');
        $guard = new TokenGuard($provider, $request);

        $this->assertNull($guard->user());
    }

    public function test_valid_string_token_still_authenticates_using_expected_credentials(): void
    {
        $provider = Mockery::mock(UserProvider::class);
        $user = Mockery::mock(Authenticatable::class);

        $provider->shouldReceive('retrieveByCredentials')
            ->once()
            ->with(['api_token' => 'known-good-token'])
            ->andReturn($user);

        $guard = new TokenGuard(
            $provider,
            Request::create('/api/protected', 'POST', ['api_token' => 'known-good-token']),
        );

        $this->assertSame($user, $guard->user());
    }

    public function test_empty_bearer_header_does_not_lookup_a_user(): void
    {
        $provider = Mockery::mock(UserProvider::class);
        $provider->shouldNotReceive('retrieveByCredentials');

        $request = Request::create('/api/protected', 'GET', [], [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ',
        ]);

        $this->assertNull((new TokenGuard($provider, $request))->user());
    }
}
