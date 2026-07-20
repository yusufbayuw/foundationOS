<?php

namespace Modules\Messaging\Providers;

use Modules\Messaging\Contracts\MessageGateway;
use Modules\Messaging\Contracts\WhatsAppProvider;
use Modules\Messaging\Services\Providers\LocalMessageGateway;
use Modules\Messaging\Services\Providers\LogWhatsAppProvider;
use Nwidart\Modules\Support\ModuleServiceProvider;

class MessagingServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'Messaging';

    protected string $nameLower = 'messaging';

    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];

    public function register(): void
    {
        parent::register();

        $this->app->singleton(WhatsAppProvider::class, LogWhatsAppProvider::class);
        $this->app->singleton(MessageGateway::class, LocalMessageGateway::class);
    }
}
