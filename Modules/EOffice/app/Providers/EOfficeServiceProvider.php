<?php

namespace Modules\EOffice\Providers;

use Nwidart\Modules\Support\ModuleServiceProvider;

class EOfficeServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'EOffice';

    protected string $nameLower = 'eoffice';

    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];
}
