<?php

namespace Modules\Asset\Providers;

use Nwidart\Modules\Support\ModuleServiceProvider;

class AssetServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'Asset';

    protected string $nameLower = 'asset';

    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];
}
