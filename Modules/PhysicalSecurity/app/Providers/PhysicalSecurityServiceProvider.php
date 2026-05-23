<?php

namespace Modules\PhysicalSecurity\Providers;

use Nwidart\Modules\Support\ModuleServiceProvider;

class PhysicalSecurityServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'PhysicalSecurity';

    protected string $nameLower = 'physicalsecurity';

    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];
}
