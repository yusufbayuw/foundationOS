<?php

namespace Modules\Messaging\Services\Providers;

use Illuminate\Support\Facades\Log;
use Modules\Messaging\Contracts\MessageGateway;
use Modules\Messaging\Contracts\WhatsAppProvider;

class LogWhatsAppProvider implements MessageGateway, WhatsAppProvider
{
    public function sendWhatsApp(string $to, string $body, array $variables = []): bool
    {
        return $this->sendMessage($to, $body, $variables);
    }

    public function sendSms(string $to, string $body, array $variables = []): bool
    {
        Log::info('sms.send', ['to' => $to, 'body' => $body, 'variables' => $variables]);

        return true;
    }

    public function sendMessage(string $to, string $body, array $variables = []): bool
    {
        Log::info('whatsapp.send', ['to' => $to, 'body' => $body, 'variables' => $variables]);

        return true;
    }

    public function sendTemplate(string $to, string $templateName, array $variables = []): bool
    {
        Log::info('whatsapp.template', ['to' => $to, 'template' => $templateName, 'variables' => $variables]);

        return true;
    }

    public function sendMedia(string $to, string $mediaUrl, ?string $caption = null): bool
    {
        Log::info('whatsapp.media', ['to' => $to, 'url' => $mediaUrl, 'caption' => $caption]);

        return true;
    }
}
