<?php

namespace Modules\Core\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Core\Support\Tenancy\CurrentTenant;

class TenancyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if (! $this->app->bound(CurrentTenant::class)) {
            $this->app->singleton(CurrentTenant::class);
        }
    }
}
