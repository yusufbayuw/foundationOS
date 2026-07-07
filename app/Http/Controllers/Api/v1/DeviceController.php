<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Requests\Api\V1\StoreDeviceRequest;
use App\Models\Device;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeviceController extends ApiController
{
    public function store(StoreDeviceRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $device = Device::updateOrCreate(
            ['user_id' => $request->user()->getKey(), 'token' => $validated['token']],
            [
                'platform' => $validated['platform'],
                'is_active' => true,
                'last_used_at' => now(),
            ],
        );

        return $this->success([
            'id' => $device->id,
            'platform' => $device->platform,
            'is_active' => $device->is_active,
            'registered_at' => $device->created_at?->toIso8601String(),
        ], 201);
    }

    public function destroy(Request $request, string $token): JsonResponse
    {
        Device::where('user_id', $request->user()->getKey())
            ->where('token', $token)
            ->update(['is_active' => false]);

        return response()->json(null, 204);
    }
}
