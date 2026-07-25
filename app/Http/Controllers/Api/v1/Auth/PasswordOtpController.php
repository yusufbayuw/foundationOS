<?php

namespace App\Http\Controllers\Api\v1\Auth;

use App\Http\Controllers\Api\v1\ApiController;
use App\Models\User;
use App\Services\Auth\OtpMessenger;
use App\Services\Auth\OtpPurpose;
use App\Services\Auth\OtpService;
use App\Services\Auth\OtpVerificationResult;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordOtpController extends ApiController
{
    public function forgot(Request $request, OtpService $otpService, OtpMessenger $messenger): JsonResponse
    {
        $validated = $request->validate([
            'identifier' => ['required', 'string', 'max:255'],
        ]);

        if (User::query()->where('email', $validated['identifier'])->orWhere('phone', $validated['identifier'])->exists()) {
            $issue = $otpService->issue($validated['identifier'], OtpPurpose::ForgotPassword);
            $messenger->send($validated['identifier'], OtpPurpose::ForgotPassword->value, $issue->plainCode);
        }

        return $this->success(['status' => 'sent']);
    }

    public function reset(Request $request, OtpService $otpService): JsonResponse
    {
        $validated = $request->validate([
            'identifier' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'digits:6'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $result = $otpService->consume($validated['identifier'], OtpPurpose::ForgotPassword, $validated['code']);

        if ($result !== OtpVerificationResult::Valid) {
            return $this->error($result->value, 'Kode OTP tidak valid atau tidak dapat digunakan.', 422);
        }

        $user = User::query()
            ->where('email', $validated['identifier'])
            ->orWhere('phone', $validated['identifier'])
            ->first();

        if ($user !== null) {
            $user->forceFill(['password' => Hash::make($validated['password'])])->save();
        }

        return $this->success(['status' => 'password_reset']);
    }
}
