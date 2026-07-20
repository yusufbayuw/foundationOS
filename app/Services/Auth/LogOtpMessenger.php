<?php

namespace App\Services\Auth;

use Illuminate\Support\Facades\Log;
use Modules\Messaging\Contracts\WhatsAppProvider;

class LogOtpMessenger implements OtpMessenger
{
    public function __construct(
        protected WhatsAppProvider $whatsApp,
    ) {}

    public function send(string $identifier, string $purpose, string $code): void
    {
        $message = "Kode OTP {$purpose} Anda adalah {$code}. Kode berlaku singkat, jangan bagikan kepada siapa pun.";

        if (filter_var($identifier, FILTER_VALIDATE_EMAIL) === false) {
            $this->whatsApp->sendMessage($identifier, $message);

            return;
        }

        Log::info('otp.email.dispatch', [
            'identifier' => $identifier,
            'purpose' => $purpose,
        ]);
    }
}
