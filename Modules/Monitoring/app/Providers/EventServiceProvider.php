<?php

namespace Modules\Monitoring\Providers;

use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Auth\Events\Login;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\Monitoring\Listeners\LogSecurityAuthEvents;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event handler mappings for the application.
     *
     * @var array<string, array<int, string>>
     */
    protected $listen = [
        Failed::class => [
            LogSecurityAuthEvents::class.'@handleFailed',
        ],
        Lockout::class => [
            LogSecurityAuthEvents::class.'@handleLockout',
        ],
        Login::class => [
            LogSecurityAuthEvents::class.'@handleLogin',
        ],
    ];

    /**
     * Indicates if events should be discovered.
     *
     * @var bool
     */
    protected static $shouldDiscoverEvents = true;

    /**
     * Configure the proper event listeners for email verification.
     */
    protected function configureEmailVerification(): void {}
}
