<?php

namespace Modules\IsoCompliance\Providers;

use Nwidart\Modules\Support\ModuleServiceProvider;

class IsoComplianceServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'IsoCompliance';

    protected string $nameLower = 'isocompliance';

    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];
}
