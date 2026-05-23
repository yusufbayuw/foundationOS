<?php

namespace Modules\Transport\Providers;

use Nwidart\Modules\Support\ModuleServiceProvider;

class TransportServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'Transport';

    protected string $nameLower = 'transport';

    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];
}
