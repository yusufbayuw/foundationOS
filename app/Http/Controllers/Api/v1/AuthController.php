<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Resources\Api\v1\TenantResource;
use App\Http\Resources\Api\v1\UserResource;
use App\Models\PersonalAccessToken;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends ApiController
{
    public function me(Request $request): JsonResponse
    {
        return $this->success(new UserResource($request->user()));
    }

    public function currentTenant(Request $request): JsonResponse
    {
        $token = $request->user()?->currentAccessToken();
        $tenant = $token instanceof PersonalAccessToken ? $token->tenant : null;

        if (! $tenant) {
            return $this->error('no_tenant', 'No tenant associated with this token.', 404);
        }

        return $this->success(new TenantResource($tenant));
    }
}
