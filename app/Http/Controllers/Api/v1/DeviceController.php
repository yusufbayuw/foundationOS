<?php

namespace App\Http\Controllers\Api\v1;

use App\Models\Device;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DeviceController extends ApiController
{
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'token' => ['required', 'string', 'max:500'],
            'platform' => ['required', 'in:android,ios,web'],
        ]);

        if ($validator->fails()) {
            return $this->error('validation_failed', 'The given data was invalid.', 422, $validator->errors()->toArray());
        }

        $device = Device::updateOrCreate(
            ['user_id' => $request->user()->getKey(), 'token' => $validator->validated()['token']],
            [
                'platform' => $validator->validated()['platform'],
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
