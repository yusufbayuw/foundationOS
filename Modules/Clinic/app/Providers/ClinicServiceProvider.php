<?php

namespace Modules\Clinic\Providers;

use Nwidart\Modules\Support\ModuleServiceProvider;

class ClinicServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'Clinic';

    protected string $nameLower = 'clinic';

    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];
}
