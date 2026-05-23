<?php

namespace Modules\Legal\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\Legal\Events\ContractExpiringSoon;
use Modules\Legal\Listeners\SendContractExpiringNotification;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        ContractExpiringSoon::class => [
            SendContractExpiringNotification::class,
        ],
    ];
}
