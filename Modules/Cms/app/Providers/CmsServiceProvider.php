<?php

namespace Modules\Cms\Providers;

use Nwidart\Modules\Support\ModuleServiceProvider;

class CmsServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'Cms';

    protected string $nameLower = 'cms';

    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];

    public function boot(): void
    {
        parent::boot();
        $this->loadViewsFrom(module_path($this->name, 'resources/views'), 'cms');
    }
}
