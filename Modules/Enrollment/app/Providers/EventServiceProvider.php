<?php

namespace Modules\Enrollment\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\Enrollment\Events\ApplicantAcceptanceReverted;
use Modules\Enrollment\Listeners\CompensateApplicantAcceptance;
use Modules\Enrollment\Listeners\UpdateRegistrationOnInvoicePaid;
use Modules\Finance\Events\StudentInvoicePaid;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event handler mappings for the application.
     *
     * @var array<string, array<int, string>>
     */
    protected $listen = [
        ApplicantAcceptanceReverted::class => [
            CompensateApplicantAcceptance::class,
        ],
        StudentInvoicePaid::class => [
            UpdateRegistrationOnInvoicePaid::class,
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
