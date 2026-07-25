<?php

namespace App\Services\Auth;

use App\Models\OtpCode;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use InvalidArgumentException;

class OtpService
{
    public const int TTL_MINUTES = 10;

    public const int MAX_ATTEMPTS = 5;

    public function issue(string $identifier, string|OtpPurpose $purpose): OtpIssue
    {
        $purpose = $this->normalizePurpose($purpose);
        $identifier = $this->normalizeIdentifier($identifier);
        $code = (string) random_int(100000, 999999);

        $otpCode = DB::transaction(function () use ($identifier, $purpose, $code): OtpCode {
            OtpCode::query()
                ->where('identifier', $identifier)
                ->where('purpose', $purpose->value)
                ->whereNull('consumed_at')
                ->update(['consumed_at' => now()]);

            return OtpCode::query()->create([
                'identifier' => $identifier,
                'purpose' => $purpose->value,
                'code_hash' => Hash::make($code),
                'expires_at' => now()->addMinutes(self::TTL_MINUTES),
                'attempts' => 0,
            ]);
        });

        return new OtpIssue($otpCode, $code);
    }

    public function verify(string $identifier, string|OtpPurpose $purpose, string $code): OtpVerificationResult
    {
        $otpCode = $this->latestActiveCode($identifier, $purpose);

        if ($otpCode === null) {
            return OtpVerificationResult::Invalid;
        }

        if ($otpCode->consumed_at !== null) {
            return OtpVerificationResult::Consumed;
        }

        if ($otpCode->expires_at->isPast()) {
            return OtpVerificationResult::Expired;
        }

        if ($otpCode->attempts >= self::MAX_ATTEMPTS) {
            return OtpVerificationResult::MaxAttemptsExceeded;
        }

        if (! Hash::check($code, $otpCode->code_hash)) {
            $otpCode->increment('attempts');

            return $otpCode->refresh()->attempts >= self::MAX_ATTEMPTS
                ? OtpVerificationResult::MaxAttemptsExceeded
                : OtpVerificationResult::Invalid;
        }

        return OtpVerificationResult::Valid;
    }

    public function consume(string $identifier, string|OtpPurpose $purpose, string $code): OtpVerificationResult
    {
        return DB::transaction(function () use ($identifier, $purpose, $code): OtpVerificationResult {
            $result = $this->verify($identifier, $purpose, $code);

            if ($result !== OtpVerificationResult::Valid) {
                return $result;
            }

            $otpCode = $this->latestActiveCode($identifier, $purpose);
            $otpCode?->forceFill(['consumed_at' => now()])->save();

            return OtpVerificationResult::Valid;
        });
    }

    protected function latestActiveCode(string $identifier, string|OtpPurpose $purpose): ?OtpCode
    {
        $purpose = $this->normalizePurpose($purpose);
        $identifier = $this->normalizeIdentifier($identifier);

        return OtpCode::query()
            ->where('identifier', $identifier)
            ->where('purpose', $purpose->value)
            ->whereNull('consumed_at')
            ->latest('id')
            ->first();
    }

    protected function normalizePurpose(string|OtpPurpose $purpose): OtpPurpose
    {
        if ($purpose instanceof OtpPurpose) {
            return $purpose;
        }

        return OtpPurpose::tryFrom($purpose) ?? throw new InvalidArgumentException('Invalid OTP purpose.');
    }

    protected function normalizeIdentifier(string $identifier): string
    {
        return Str::lower(trim($identifier));
    }
}
