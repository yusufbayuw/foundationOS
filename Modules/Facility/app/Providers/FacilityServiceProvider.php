<?php

namespace Modules\Facility\Providers;

use Nwidart\Modules\Support\ModuleServiceProvider;

class FacilityServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'Facility';

    protected string $nameLower = 'facility';

    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];
}
