<?php

namespace App\Services\Auth;

use App\Models\UserPasskey;
use Illuminate\Support\Facades\Cache;
use InvalidArgumentException;
use Modules\Core\Models\User;

class PasskeyChallengeService
{
    private const REGISTER_TTL_SECONDS = 300;

    private const LOGIN_TTL_SECONDS = 300;

    public function registerOptions(User $user): array
    {
        $challenge = $this->newChallenge();
        Cache::put($this->registerCacheKey($user->id, $challenge), true, self::REGISTER_TTL_SECONDS);

        return [
            'challenge' => $challenge,
            'rp' => ['name' => config('app.name'), 'id' => parse_url(config('app.url'), PHP_URL_HOST) ?: 'localhost'],
            'user' => ['id' => (string) $user->id, 'name' => $user->email, 'displayName' => $user->name],
            'pubKeyCredParams' => [['type' => 'public-key', 'alg' => -7], ['type' => 'public-key', 'alg' => -257]],
            'timeout' => self::REGISTER_TTL_SECONDS * 1000,
            'attestation' => 'none',
            'authenticatorSelection' => ['residentKey' => 'preferred', 'userVerification' => 'preferred'],
            'excludeCredentials' => $user->passkeys()->whereNull('disabled_at')->get(['credential_id', 'transports'])->map(fn (UserPasskey $passkey): array => [
                'type' => 'public-key',
                'id' => $passkey->credential_id,
                'transports' => $passkey->transports ?? [],
            ])->all(),
            'biometric_notice' => 'Biometric verification stays on the user device; the server stores only the WebAuthn credential ID and public key.',
        ];
    }

    public function loginOptions(?User $user = null): array
    {
        $challenge = $this->newChallenge();
        Cache::put($this->loginCacheKey($challenge), ['user_id' => $user?->id], self::LOGIN_TTL_SECONDS);

        return [
            'challenge' => $challenge,
            'timeout' => self::LOGIN_TTL_SECONDS * 1000,
            'userVerification' => 'preferred',
            'allowCredentials' => $user?->passkeys()->whereNull('disabled_at')->get(['credential_id', 'transports'])->map(fn (UserPasskey $passkey): array => [
                'type' => 'public-key',
                'id' => $passkey->credential_id,
                'transports' => $passkey->transports ?? [],
            ])->all() ?? [],
        ];
    }

    public function consumeRegisterChallenge(User $user, string $challenge): void
    {
        $key = $this->registerCacheKey($user->id, $challenge);
        if (! Cache::pull($key)) {
            throw new InvalidArgumentException('The passkey registration challenge is invalid or has expired.');
        }
    }

    public function consumeLoginChallenge(string $challenge): array
    {
        $key = $this->loginCacheKey($challenge);
        $payload = Cache::pull($key);
        if (! is_array($payload)) {
            throw new InvalidArgumentException('The passkey login challenge is invalid, expired, or already used.');
        }

        return $payload;
    }

    private function newChallenge(): string
    {
        return rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
    }

    private function registerCacheKey(int $userId, string $challenge): string
    {
        return "passkeys:register:{$userId}:{$challenge}";
    }

    private function loginCacheKey(string $challenge): string
    {
        return "passkeys:login:{$challenge}";
    }
}
