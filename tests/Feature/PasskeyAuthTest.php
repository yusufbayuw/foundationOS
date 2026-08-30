<?php

namespace Tests\Feature;

use App\Models\UserPasskey;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Modules\Core\Models\User;
use Tests\TestCase;

class PasskeyAuthTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_registration_options_generate_challenge_without_collecting_biometrics(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['api:write']);

        $response = $this->postJson('/api/v1/auth/passkeys/options/register');

        $response->assertOk()
            ->assertJsonPath('data.rp.name', config('app.name'))
            ->assertJsonPath('data.attestation', 'none')
            ->assertJsonPath('data.biometric_notice', 'Biometric verification stays on the user device; the server stores only the WebAuthn credential ID and public key.');

        $this->assertNotEmpty($response->json('data.challenge'));
    }

    public function test_user_can_register_passkey_public_credential(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['api:write']);

        $challenge = $this->postJson('/api/v1/auth/passkeys/options/register')
            ->json('data.challenge');

        $publicKey = $this->makeKeyPair()['public'];

        $this->postJson('/api/v1/auth/passkeys/register', [
            'challenge' => $challenge,
            'credential_id' => 'credential-register-1',
            'public_key' => trim($publicKey),
            'sign_count' => 10,
            'transports' => ['internal'],
        ])
            ->assertCreated()
            ->assertJsonPath('data.credential_id', 'credential-register-1')
            ->assertJsonPath('data.biometric_notice', 'Biometric templates never leave the device; only WebAuthn credential material is stored.');

        $this->assertDatabaseHas('user_passkeys', [
            'user_id' => $user->id,
            'credential_id' => 'credential-register-1',
            'public_key' => trim($publicKey),
            'sign_count' => 10,
        ]);
    }

    public function test_read_only_token_cannot_register_a_passkey(): void
    {
        Sanctum::actingAs(User::factory()->create(), ['api:read']);

        $this->postJson('/api/v1/auth/passkeys/options/register')
            ->assertForbidden();
    }

    public function test_user_can_login_with_valid_passkey_assertion(): void
    {
        $user = User::factory()->create(['email' => 'passkey@example.com']);
        $keys = $this->makeKeyPair();
        UserPasskey::create([
            'user_id' => $user->id,
            'credential_id' => 'credential-login-1',
            'public_key' => $keys['public'],
            'sign_count' => 1,
            'transports' => ['internal'],
        ]);
        $challenge = $this->postJson('/api/v1/auth/passkeys/options/login', ['email' => 'passkey@example.com'])->json('data.challenge');
        $assertion = $this->makeAssertion($keys['private'], $challenge);

        $this->postJson('/api/v1/auth/passkeys/login', [
            'challenge' => $challenge,
            'credential_id' => 'credential-login-1',
            'signed_data' => $assertion['signed_data'],
            'signature' => $assertion['signature'],
            'sign_count' => 2,
        ])->assertOk()
            ->assertJsonPath('data.user.email', 'passkey@example.com')
            ->assertJsonStructure(['data' => ['token']]);

        $this->assertDatabaseHas('user_passkeys', [
            'credential_id' => 'credential-login-1',
            'sign_count' => 2,
        ]);
    }

    public function test_replayed_assertion_is_rejected(): void
    {
        $user = User::factory()->create();
        $keys = $this->makeKeyPair();
        UserPasskey::create([
            'user_id' => $user->id,
            'credential_id' => 'credential-replay-1',
            'public_key' => $keys['public'],
            'sign_count' => 7,
        ]);
        $challenge = $this->postJson('/api/v1/auth/passkeys/options/login')->json('data.challenge');
        $assertion = $this->makeAssertion($keys['private'], $challenge);

        $this->postJson('/api/v1/auth/passkeys/login', [
            'challenge' => $challenge,
            'credential_id' => 'credential-replay-1',
            'signed_data' => $assertion['signed_data'],
            'signature' => $assertion['signature'],
            'sign_count' => 7,
        ])->assertStatus(409)
            ->assertJsonPath('error.code', 'replayed_assertion');
    }

    public function test_disabled_credential_is_rejected(): void
    {
        $user = User::factory()->create();
        $keys = $this->makeKeyPair();
        UserPasskey::create([
            'user_id' => $user->id,
            'credential_id' => 'credential-disabled-1',
            'public_key' => $keys['public'],
            'sign_count' => 1,
            'disabled_at' => now(),
        ]);
        $challenge = $this->postJson('/api/v1/auth/passkeys/options/login')->json('data.challenge');
        $assertion = $this->makeAssertion($keys['private'], $challenge);

        $this->postJson('/api/v1/auth/passkeys/login', [
            'challenge' => $challenge,
            'credential_id' => 'credential-disabled-1',
            'signed_data' => $assertion['signed_data'],
            'signature' => $assertion['signature'],
            'sign_count' => 2,
        ])->assertForbidden()
            ->assertJsonPath('error.code', 'credential_disabled');
    }

    /**
     * @return array{private: string, public: string}
     */
    private function makeKeyPair(): array
    {
        $privateKey = openssl_pkey_new([
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
            'private_key_bits' => 2048,
        ]);

        openssl_pkey_export($privateKey, $privatePem);
        $publicPem = openssl_pkey_get_details($privateKey)['key'];

        return ['private' => $privatePem, 'public' => $publicPem];
    }

    /**
     * @return array{signed_data: string, signature: string}
     */
    private function makeAssertion(string $privateKey, string $challenge): array
    {
        $signedData = base64_encode(json_encode(['challenge' => $challenge], JSON_THROW_ON_ERROR));
        openssl_sign($signedData, $signature, $privateKey, OPENSSL_ALGO_SHA256);

        return ['signed_data' => $signedData, 'signature' => base64_encode($signature)];
    }
}
