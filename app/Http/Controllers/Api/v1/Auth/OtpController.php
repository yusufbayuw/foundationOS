<?php

namespace App\Http\Controllers\Api\v1\Auth;

use App\Http\Controllers\Api\v1\ApiController;
use App\Services\Auth\OtpMessenger;
use App\Services\Auth\OtpPurpose;
use App\Services\Auth\OtpService;
use App\Services\Auth\OtpVerificationResult;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OtpController extends ApiController
{
    public function request(Request $request, OtpService $otpService, OtpMessenger $messenger): JsonResponse
    {
        $validated = $request->validate([
            'identifier' => ['required', 'string', 'max:255'],
            'purpose' => ['required', Rule::enum(OtpPurpose::class)],
        ]);

        $issue = $otpService->issue($validated['identifier'], $validated['purpose']);
        $messenger->send($validated['identifier'], $validated['purpose'], $issue->plainCode);

        return $this->success([
            'status' => 'sent',
            'expires_at' => $issue->otpCode->expires_at->toISOString(),
        ], 201);
    }

    public function verify(Request $request, OtpService $otpService): JsonResponse
    {
        $validated = $request->validate([
            'identifier' => ['required', 'string', 'max:255'],
            'purpose' => ['required', Rule::enum(OtpPurpose::class)],
            'code' => ['required', 'string', 'digits:6'],
        ]);

        $result = $otpService->verify($validated['identifier'], $validated['purpose'], $validated['code']);

        if ($result !== OtpVerificationResult::Valid) {
            return $this->error($result->value, 'Kode OTP tidak valid atau tidak dapat digunakan.', 422);
        }

        return $this->success(['status' => 'valid']);
    }
}
