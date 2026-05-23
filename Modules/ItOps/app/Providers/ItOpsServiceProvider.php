<?php

namespace Modules\ItOps\Providers;

use Modules\ItOps\Contracts\MikroTikClient;
use Modules\ItOps\Contracts\MonitoringWebhookReceiver;
use Modules\ItOps\Services\ItMonitoringWebhookReceiver;
use Modules\ItOps\Services\NullMikroTikClient;
use Nwidart\Modules\Support\ModuleServiceProvider;

class ItOpsServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'ItOps';

    protected string $nameLower = 'itops';

    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];

    public function register(): void
    {
        parent::register();

        $this->app->bind(MikroTikClient::class, NullMikroTikClient::class);
        $this->app->bind(MonitoringWebhookReceiver::class, ItMonitoringWebhookReceiver::class);
    }
}
