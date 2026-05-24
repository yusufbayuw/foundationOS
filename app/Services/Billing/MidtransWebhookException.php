<?php

namespace App\Services\Billing;

use RuntimeException;

class MidtransWebhookException extends RuntimeException
{
    public static function invalidSignature(): self
    {
        return new self('Invalid Midtrans webhook signature.');
    }

    public static function missingField(string $field): self
    {
        return new self("Missing Midtrans webhook field [{$field}].");
    }
}
