<?php

namespace App\Services\Auth;

enum OtpPurpose: string
{
    case Registration = 'registration';
    case Login = 'login';
    case ForgotPassword = 'forgot_password';
    case AccountVerification = 'account_verification';
}
