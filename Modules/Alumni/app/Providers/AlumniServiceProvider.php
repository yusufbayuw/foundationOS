<?php

namespace Modules\Alumni\Providers;

use Nwidart\Modules\Support\ModuleServiceProvider;

class AlumniServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'Alumni';

    protected string $nameLower = 'alumni';

    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];
}
