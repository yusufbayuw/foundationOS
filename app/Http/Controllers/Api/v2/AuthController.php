<?php

namespace App\Http\Controllers\Api\v2;

use App\Http\Resources\Api\v1\TenantResource;
use App\Http\Resources\Api\v1\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends ApiController
{
    public function me(Request $request): JsonResponse
    {
        return $this->success(
            new UserResource($request->user()),
            meta: ['api_version' => $this->apiVersion()],
        );
    }

    public function currentTenant(Request $request): JsonResponse
    {
        $tenant = $request->user()?->currentAccessToken()?->tenant;

        if (! $tenant) {
            return $this->error('no_tenant', 'No tenant associated with this token.', 404);
        }

        return $this->success(
            new TenantResource($tenant),
            meta: ['api_version' => $this->apiVersion()],
        );
    }
}
