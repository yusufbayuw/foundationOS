<?php

namespace Modules\ItOps\Services;

use Illuminate\Support\Facades\Log;
use Modules\ItOps\Contracts\MonitoringWebhookReceiver;
use Modules\ItOps\Events\ItIncidentReceived;

class ItMonitoringWebhookReceiver implements MonitoringWebhookReceiver
{
    public function receive(array $payload): void
    {
        Log::info('IT monitoring webhook received', ['payload' => $payload]);

        event(new ItIncidentReceived($payload));
    }
}
