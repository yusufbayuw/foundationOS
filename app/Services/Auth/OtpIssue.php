<?php

namespace App\Services\Auth;

use App\Models\OtpCode;

class OtpIssue
{
    public function __construct(
        public OtpCode $otpCode,
        public string $plainCode,
    ) {}
}
