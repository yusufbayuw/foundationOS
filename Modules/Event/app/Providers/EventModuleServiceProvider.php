<?php

namespace Modules\Event\Providers;

use Nwidart\Modules\Support\ModuleServiceProvider;

class EventModuleServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'Event';

    protected string $nameLower = 'event';

    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];
}
