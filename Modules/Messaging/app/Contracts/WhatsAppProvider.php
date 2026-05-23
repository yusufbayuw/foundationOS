<?php

namespace Modules\Messaging\Contracts;

interface WhatsAppProvider
{
    /**
     * @param  array<string, mixed>  $variables
     */
    public function sendMessage(string $to, string $body, array $variables = []): bool;

    /**
     * @param  array<string, mixed>  $variables
     */
    public function sendTemplate(string $to, string $templateName, array $variables = []): bool;

    public function sendMedia(string $to, string $mediaUrl, ?string $caption = null): bool;
}
