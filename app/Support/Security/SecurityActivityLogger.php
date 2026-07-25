<?php

namespace App\Support\Security;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Enums\ActivityEvent;

class SecurityActivityLogger
{
    /**
     * @param  array<string, mixed>  $properties
     */
    public function voucherRedeemed(Model $voucher, ?Model $causer = null, array $properties = []): void
    {
        activity()
            ->performedOn($voucher)
            ->causedBy($causer)
            ->event(ActivityEvent::Updated)
            ->withProperties($properties)
            ->log('Voucher redeemed');
    }

    /**
     * @param  array<string, mixed>  $properties
     */
    public function otpSuspiciousAttempt(?Model $causer = null, array $properties = []): void
    {
        activity('security')
            ->causedBy($causer)
            ->event('otp.suspicious')
            ->withProperties($properties)
            ->log('Suspicious OTP attempt detected');
    }
}
