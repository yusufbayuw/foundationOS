<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Resources\Api\v1\TenantResource;
use App\Http\Resources\Api\v1\UserResource;
use App\Support\CurrentTenant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends ApiController
{
    public function __construct(private readonly CurrentTenant $currentTenant) {}

    public function me(Request $request): JsonResponse
    {
        return $this->success(new UserResource($request->user()));
    }

    public function currentTenant(Request $request): JsonResponse
    {
        $tenant = $this->currentTenant->model()
            ?? $request->user()?->currentAccessToken()?->tenant;

        if (! $tenant) {
            return $this->error('no_tenant', 'No tenant associated with this token.', 404);
        }

        return $this->success(new TenantResource($tenant));
    }
}
