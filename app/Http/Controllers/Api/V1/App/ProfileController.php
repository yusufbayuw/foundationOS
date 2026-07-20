<?php

namespace App\Http\Controllers\Api\V1\App;

use App\Http\Controllers\Api\v1\ApiController;
use App\Http\Resources\Api\V1\App\ProfileResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends ApiController
{
    public function show(Request $request): JsonResponse
    {
        return $this->success(new ProfileResource($request->user()));
    }

    public function update(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'phone' => ['sometimes', 'nullable', 'string', 'max:50'],
            'timezone' => ['sometimes', 'nullable', 'string', 'max:100'],
            'locale' => ['sometimes', 'nullable', 'string', 'max:20'],
        ]);

        $request->user()->update($data);

        return $this->success(new ProfileResource($request->user()->refresh()));
    }
}
