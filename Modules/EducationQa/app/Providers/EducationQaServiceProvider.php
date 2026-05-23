<?php

namespace Modules\EducationQa\Providers;

use Nwidart\Modules\Support\ModuleServiceProvider;

class EducationQaServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'EducationQa';

    protected string $nameLower = 'educationqa';

    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];
}
