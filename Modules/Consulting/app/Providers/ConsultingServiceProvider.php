<?php

namespace Modules\Consulting\Providers;

use Nwidart\Modules\Support\ModuleServiceProvider;

class ConsultingServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'Consulting';

    protected string $nameLower = 'consulting';

    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];
}
