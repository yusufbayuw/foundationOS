<?php

namespace Modules\ItOps\Contracts;

interface MonitoringWebhookReceiver
{
    /**
     * @param  array<string, mixed>  $payload
     */
    public function receive(array $payload): void;
}
