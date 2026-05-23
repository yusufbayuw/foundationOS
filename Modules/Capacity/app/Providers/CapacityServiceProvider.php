<?php

namespace Modules\Capacity\Providers;

use Nwidart\Modules\Support\ModuleServiceProvider;

class CapacityServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'Capacity';

    protected string $nameLower = 'capacity';

    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];
}
