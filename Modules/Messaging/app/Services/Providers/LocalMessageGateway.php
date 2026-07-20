<?php

namespace Modules\Messaging\Services\Providers;

use Illuminate\Support\Facades\Log;
use Modules\Messaging\Contracts\MessageGateway;

class LocalMessageGateway implements MessageGateway
{
    /** @var list<array{channel: string, to: string, body: string, variables: array<string, mixed>}> */
    public array $messages = [];

    /**
     * @param  array<string, mixed>  $variables
     */
    public function sendWhatsApp(string $to, string $body, array $variables = []): bool
    {
        return $this->record('whatsapp', $to, $body, $variables);
    }

    /**
     * @param  array<string, mixed>  $variables
     */
    public function sendSms(string $to, string $body, array $variables = []): bool
    {
        return $this->record('sms', $to, $body, $variables);
    }

    /**
     * @param  array<string, mixed>  $variables
     */
    protected function record(string $channel, string $to, string $body, array $variables): bool
    {
        $this->messages[] = compact('channel', 'to', 'body', 'variables');

        Log::info("message_gateway.{$channel}.send", compact('to', 'body', 'variables'));

        return true;
    }
}
