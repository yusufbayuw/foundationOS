<?php

namespace Modules\Training\Providers;

use Nwidart\Modules\Support\ModuleServiceProvider;

class TrainingServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'Training';

    protected string $nameLower = 'training';

    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];
}
