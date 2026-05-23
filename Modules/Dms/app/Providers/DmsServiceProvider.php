<?php

namespace Modules\Dms\Providers;

use Nwidart\Modules\Support\ModuleServiceProvider;

class DmsServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'Dms';

    protected string $nameLower = 'dms';

    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];
}
