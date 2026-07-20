<?php

namespace App\Services\Auth;

interface OtpMessenger
{
    public function send(string $identifier, string $purpose, string $code): void;
}
