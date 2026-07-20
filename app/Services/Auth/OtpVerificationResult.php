<?php

namespace App\Services\Auth;

enum OtpVerificationResult: string
{
    case Valid = 'valid';
    case Invalid = 'invalid';
    case Expired = 'expired';
    case MaxAttemptsExceeded = 'max_attempts_exceeded';
    case Consumed = 'consumed';
}
