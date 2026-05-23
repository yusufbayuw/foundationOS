<?php

namespace Modules\MerchOrder\Providers;

use Nwidart\Modules\Support\ModuleServiceProvider;

class MerchOrderServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'MerchOrder';

    protected string $nameLower = 'merchorder';

    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];
}
