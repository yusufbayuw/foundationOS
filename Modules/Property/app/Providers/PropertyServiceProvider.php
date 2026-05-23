<?php

namespace Modules\Property\Providers;

use Nwidart\Modules\Support\ModuleServiceProvider;

class PropertyServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'Property';

    protected string $nameLower = 'property';

    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];
}
