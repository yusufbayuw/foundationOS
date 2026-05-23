<?php

namespace Modules\KpiEnterprise\Providers;

use Nwidart\Modules\Support\ModuleServiceProvider;

class KpiEnterpriseServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'KpiEnterprise';

    protected string $nameLower = 'kpienterprise';

    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];
}
