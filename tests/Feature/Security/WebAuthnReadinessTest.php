<?php

namespace Tests\Feature\Security;

use App\Models\UserPasskey;
use App\Support\Security\WebAuthnRpConfiguration;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Route;
use Laravel\Passkeys\Passkey;
use Modules\Core\Models\User;
use RuntimeException;
use Tests\TestCase;

class WebAuthnReadinessTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_verification_routes_are_not_publicly_exposed_before_security_acceptance(): void
    {
        $this->assertFalse(config('passkeys.enabled'));
        $this->assertFalse(Route::has('passkey.login'));
        $this->assertFalse(Route::has('passkey.store'));

        $this->getJson('/passkeys/login/options')->assertNotFound();
        $this->postJson('/passkeys/login', [
            'id' => 'forged',
            'response' => ['clientDataJSON' => 'forged'],
        ])->assertNotFound();

        $this->postJson('/api/v1/auth/passkeys/login', [
            'credential_id' => 'legacy',
            'signed_data' => 'forged',
            'signature' => 'forged',
        ])->assertStatus(503)->assertJsonPath('error.code', 'passkey_unavailable');

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_verified_credentials_are_independent_of_legacy_unsigned_public_keys(): void
    {
        $user = User::factory()->create();
        UserPasskey::query()->create([
            'user_id' => $user->getKey(),
            'credential_id' => 'legacy-credential',
            'public_key' => 'untrusted-client-key',
            'sign_count' => 10,
        ]);

        $this->assertSame('passkeys', (new Passkey)->getTable());
        $this->assertSame('user_passkeys', (new UserPasskey)->getTable());
        $this->assertSame(1, $user->legacyPasskeys()->count());
        $this->assertSame(0, $user->passkeys()->count());
        $this->assertDatabaseCount('passkeys', 0);
    }

    public function test_webauthn_configuration_rejects_untrusted_origins_and_rp_ids(): void
    {
        WebAuthnRpConfiguration::assertValid('login.example.edu', ['https://login.example.edu']);

        $invalid = [
            ['login.example.edu', []],
            ['login.example.edu', ['https://login.example.edu', 'https://evil.example']],
            ['login.example.edu', ['http://login.example.edu']],
            ['login.example.edu', ['https://evil.example.edu']],
            ['login.example.edu', ['https://login.example.edu/evil']],
            ['login.example.edu', ['https://user@login.example.edu']],
            ['login.example.edu', ['https://login.example.edu?next=evil']],
            ['*.example.edu', ['https://login.example.edu']],
            ['', ['https://login.example.edu']],
        ];

        foreach ($invalid as [$rpId, $origins]) {
            try {
                WebAuthnRpConfiguration::assertValid($rpId, $origins);
                $this->fail("Configuration should reject the RP ID / origin: {$rpId}");
            } catch (RuntimeException) {
                $this->assertTrue(true);
            }
        }
    }
}
