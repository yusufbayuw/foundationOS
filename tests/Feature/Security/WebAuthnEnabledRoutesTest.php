<?php

namespace Tests\Feature\Security;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tests\TestCase;

#[RunTestsInSeparateProcesses]
#[PreserveGlobalState(false)]
class WebAuthnEnabledRoutesTest extends TestCase
{
    use LazilyRefreshDatabase;

    /**
     * Boot an independent Laravel app with explicit RP configuration.
     * This exercises the opt-in routes without enabling them in other tests.
     */
    protected function setUp(): void
    {
        foreach ([
            'PASSKEYS_ENABLED' => 'true',
            'PASSKEYS_ORIGIN' => 'https://localhost',
            'PASSKEYS_RP_ID' => 'localhost',
        ] as $name => $value) {
            putenv("{$name}={$value}");
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }

        parent::setUp();
    }

    public function test_enabled_relying_party_issues_standard_browser_options_without_access_token(): void
    {
        $response = $this->getJson('/passkeys/login/options');

        $response->assertOk()
            ->assertJsonStructure(['options' => ['challenge']]);

        $this->assertNotEmpty($response->json('options.challenge'));
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_empty_forged_assertion_is_rejected_before_authentication(): void
    {
        $this->getJson('/passkeys/login/options')->assertOk();

        $this->postJson('/passkeys/login', [
            'credential' => [],
        ])->assertUnprocessable();

        $this->assertGuest('web');
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_guest_cannot_create_verified_credentials(): void
    {
        $this->getJson('/user/passkeys/options')->assertUnauthorized();
        $this->postJson('/user/passkeys', [
            'credential' => ['id' => 'attacker'],
        ])->assertUnauthorized();

        $this->assertDatabaseCount('passkeys', 0);
    }
}
