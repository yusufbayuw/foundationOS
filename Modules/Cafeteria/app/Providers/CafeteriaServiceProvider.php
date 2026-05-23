<?php

namespace Modules\Cafeteria\Providers;

use Nwidart\Modules\Support\ModuleServiceProvider;

class CafeteriaServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'Cafeteria';

    protected string $nameLower = 'cafeteria';

    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];
}
