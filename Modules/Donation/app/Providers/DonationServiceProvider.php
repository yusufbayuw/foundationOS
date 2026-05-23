<?php

namespace Modules\Donation\Providers;

use Nwidart\Modules\Support\ModuleServiceProvider;

class DonationServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'Donation';

    protected string $nameLower = 'donation';

    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];
}
