<?php

namespace App\Http\Controllers\Api\v1\Auth;

use App\Http\Controllers\Api\v1\ApiController;
use App\Models\UserPasskey;
use App\Services\Auth\PasskeyChallengeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use InvalidArgumentException;
use Modules\Core\Models\User;

class PasskeyController extends ApiController
{
    public function registerOptions(Request $request, PasskeyChallengeService $challenges): JsonResponse
    {
        return $this->success($challenges->registerOptions($request->user()));
    }

    public function register(Request $request, PasskeyChallengeService $challenges): JsonResponse
    {
        $data = $request->validate([
            'challenge' => ['required', 'string'],
            'credential_id' => ['required', 'string', 'max:1024', Rule::unique('user_passkeys', 'credential_id')],
            'public_key' => ['required', 'string'],
            'sign_count' => ['nullable', 'integer', 'min:0'],
            'transports' => ['nullable', 'array'],
            'transports.*' => ['string', 'max:32'],
        ]);

        try {
            $challenges->consumeRegisterChallenge($request->user(), $data['challenge']);
        } catch (InvalidArgumentException $exception) {
            return $this->error('invalid_challenge', $exception->getMessage(), 422);
        }

        $passkey = $request->user()->passkeys()->create([
            'credential_id' => $data['credential_id'],
            'public_key' => trim($data['public_key']),
            'sign_count' => $data['sign_count'] ?? 0,
            'transports' => $data['transports'] ?? [],
        ]);

        return $this->success([
            'id' => $passkey->id,
            'credential_id' => $passkey->credential_id,
            'transports' => $passkey->transports,
            'biometric_notice' => 'Biometric templates never leave the device; only WebAuthn credential material is stored.',
        ], 201);
    }

    public function loginOptions(Request $request, PasskeyChallengeService $challenges): JsonResponse
    {
        $data = $request->validate(['email' => ['nullable', 'email']]);
        $user = isset($data['email']) ? User::query()->where('email', $data['email'])->first() : null;

        return $this->success($challenges->loginOptions($user));
    }

    public function login(Request $request, PasskeyChallengeService $challenges): JsonResponse
    {
        $data = $request->validate([
            'challenge' => ['required', 'string'],
            'credential_id' => ['required', 'string'],
            'signature' => ['required', 'string'],
            'signed_data' => ['required', 'string'],
            'sign_count' => ['required', 'integer', 'min:0'],
        ]);

        try {
            $challengePayload = $challenges->consumeLoginChallenge($data['challenge']);
        } catch (InvalidArgumentException $exception) {
            return $this->error('invalid_challenge', $exception->getMessage(), 422);
        }

        $passkey = UserPasskey::query()->where('credential_id', $data['credential_id'])->with('user')->first();

        if (! $passkey || ! $passkey->isEnabled()) {
            return $this->error('credential_disabled', 'The passkey credential is disabled or unavailable.', 403);
        }

        if ($challengePayload['user_id'] !== null && $passkey->user_id !== $challengePayload['user_id']) {
            return $this->error('credential_mismatch', 'The passkey does not belong to the requested user.', 403);
        }

        if ($data['sign_count'] <= $passkey->sign_count) {
            return $this->error('replayed_assertion', 'The passkey assertion sign count was already used.', 409);
        }

        $signedPayload = json_decode(base64_decode($data['signed_data'], true) ?: '', true);
        if (! is_array($signedPayload) || ($signedPayload['challenge'] ?? null) !== $data['challenge']) {
            return $this->error('invalid_assertion', 'The signed payload does not match the issued challenge.', 422);
        }

        $verified = openssl_verify($data['signed_data'], base64_decode($data['signature'], true) ?: '', $passkey->public_key, OPENSSL_ALGO_SHA256) === 1;
        if (! $verified) {
            return $this->error('invalid_signature', 'The passkey assertion signature could not be verified.', 422);
        }

        $passkey->forceFill([
            'sign_count' => $data['sign_count'],
            'last_used_at' => now(),
        ])->save();

        return $this->success([
            'token' => $passkey->user->createToken('passkey-login')->plainTextToken,
            'user' => ['id' => $passkey->user->id, 'email' => $passkey->user->email, 'name' => $passkey->user->name],
        ]);
    }
}
