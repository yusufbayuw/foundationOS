<?php

namespace Modules\Printing\Providers;

use Nwidart\Modules\Support\ModuleServiceProvider;

class PrintingServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'Printing';

    protected string $nameLower = 'printing';

    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];
}
