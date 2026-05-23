<?php

namespace Modules\InternalAudit\Providers;

use Nwidart\Modules\Support\ModuleServiceProvider;

class InternalAuditServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'InternalAudit';

    protected string $nameLower = 'internalaudit';

    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];
}
