<?php

namespace App\Http\Controllers\Api\v1\Auth;

use App\Http\Controllers\Api\v1\ApiController;
use Illuminate\Http\JsonResponse;

class PasskeyController extends ApiController
{
    /**
     * The former signed-message flow did not validate WebAuthn ceremonies.
     * Keep the existing URLs for clients, but fail closed until a vetted
     * WebAuthn relying-party implementation replaces the legacy flow.
     */
    public function registerOptions(): JsonResponse
    {
        return $this->unavailable();
    }

    public function register(): JsonResponse
    {
        return $this->unavailable();
    }

    public function loginOptions(): JsonResponse
    {
        return $this->unavailable();
    }

    public function login(): JsonResponse
    {
        return $this->unavailable();
    }

    private function unavailable(): JsonResponse
    {
        return $this->error(
            'passkey_unavailable',
            'Passkey authentication is unavailable pending standards-compliant WebAuthn verification. Use another supported sign-in method.',
            503,
        );
    }
}
