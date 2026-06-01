<?php

namespace Modules\Finance\Listeners;

use App\Concerns\InteractsWithTenant;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Modules\Enrollment\Events\ApplicantAccepted;
use Modules\Finance\Services\ApplicantOnboardingInvoiceService;
use Modules\Monitoring\Services\AutomationRunLogger;

class CreateInitialInvoiceFromAcceptedApplicant implements ShouldQueue
{
    use InteractsWithTenant, Queueable;

    public function __construct()
    {
        $this->captureCurrentTenant();
    }

    public function handle(ApplicantAccepted $event): void
    {
        app(AutomationRunLogger::class)->run(
            ApplicantAccepted::class,
            $event->applicant,
            fn () => app(ApplicantOnboardingInvoiceService::class)->createDraftFor($event->applicant, $event->actor),
            'create_registration_invoice',
        );
    }
}
