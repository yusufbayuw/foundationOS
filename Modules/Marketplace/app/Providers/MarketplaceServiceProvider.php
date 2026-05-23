<?php

namespace Modules\Marketplace\Providers;

use Nwidart\Modules\Support\ModuleServiceProvider;

class MarketplaceServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'Marketplace';

    protected string $nameLower = 'marketplace';

    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];
}
