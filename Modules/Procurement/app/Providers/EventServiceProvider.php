<?php

namespace Modules\Procurement\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\Procurement\Events\PurchaseRequisitionApproved;
use Modules\Procurement\Listeners\CreateRfqFromApprovedPurchaseRequisition;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event handler mappings for the application.
     *
     * @var array<string, array<int, string>>
     */
    protected $listen = [
        PurchaseRequisitionApproved::class => [
            CreateRfqFromApprovedPurchaseRequisition::class,
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
