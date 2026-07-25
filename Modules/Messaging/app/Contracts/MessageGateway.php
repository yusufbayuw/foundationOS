<?php

namespace Modules\Messaging\Contracts;

interface MessageGateway
{
    /**
     * @param  array<string, mixed>  $variables
     */
    public function sendWhatsApp(string $to, string $body, array $variables = []): bool;

    /**
     * @param  array<string, mixed>  $variables
     */
    public function sendSms(string $to, string $body, array $variables = []): bool;
}
