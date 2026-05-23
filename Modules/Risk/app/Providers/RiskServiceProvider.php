<?php

namespace Modules\Risk\Providers;

use Nwidart\Modules\Support\ModuleServiceProvider;

class RiskServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'Risk';

    protected string $nameLower = 'risk';

    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];
}
