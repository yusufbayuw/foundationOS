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

    public function test_public_legacy_passkey_routes_fail_closed_without_issuing_challenges(): void
    {
        foreach (['options/login', 'login'] as $endpoint) {
            $this->postJson("/api/v1/auth/passkeys/{$endpoint}", [
                'credential_id' => 'legacy-credential',
            ])->assertStatus(503)
                ->assertJsonPath('error.code', 'passkey_unavailable')
                ->assertJsonMissingPath('data.challenge')
                ->assertJsonMissingPath('data.token');
        }
    }

    public function test_authenticated_legacy_registration_routes_fail_closed_without_persisting_keys(): void
    {
        Sanctum::actingAs(User::factory()->create(), ['api:write']);

        foreach (['options/register', 'register'] as $endpoint) {
            $this->postJson("/api/v1/auth/passkeys/{$endpoint}", [
                'challenge' => 'forged-challenge',
                'credential_id' => 'forged-credential',
                'public_key' => 'forged-public-key',
            ])->assertStatus(503)
                ->assertJsonPath('error.code', 'passkey_unavailable');
        }

        $this->assertDatabaseCount('user_passkeys', 0);
    }

    public function test_legacy_credentials_remain_stored_but_cannot_issue_access_tokens(): void
    {
        $user = User::factory()->create();
        UserPasskey::query()->create([
            'user_id' => $user->id,
            'credential_id' => 'existing-legacy-credential',
            'public_key' => 'legacy-key',
            'sign_count' => 8,
            'transports' => ['internal'],
        ]);

        $this->postJson('/api/v1/auth/passkeys/login', [
            'challenge' => 'arbitrary-challenge',
            'credential_id' => 'existing-legacy-credential',
            'signature' => 'arbitrary-signature',
            'signed_data' => 'arbitrary-payload',
            'sign_count' => 9,
        ])->assertStatus(503)
            ->assertJsonPath('error.code', 'passkey_unavailable');

        $this->assertDatabaseHas('user_passkeys', [
            'user_id' => $user->id,
            'credential_id' => 'existing-legacy-credential',
            'sign_count' => 8,
        ]);
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_read_only_tokens_still_cannot_reach_registration(): void
    {
        Sanctum::actingAs(User::factory()->create(), ['api:read']);

        $this->postJson('/api/v1/auth/passkeys/register')
            ->assertForbidden();
    }
}
