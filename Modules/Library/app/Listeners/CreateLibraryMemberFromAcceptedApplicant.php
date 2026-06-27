<?php

namespace Modules\Library\Listeners;

use App\Concerns\InteractsWithTenant;
use App\Support\TypedValue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Modules\Enrollment\Events\ApplicantAccepted;
use Modules\Enrollment\Services\ApplicantPromotionService;
use Modules\Library\Services\LibraryMemberProvisioningService;
use Modules\Monitoring\Services\AutomationRunLogger;

class CreateLibraryMemberFromAcceptedApplicant implements ShouldQueue
{
    use InteractsWithTenant, Queueable;

    public function __construct()
    {
        $this->captureCurrentTenant();
    }

    public function handle(ApplicantAccepted $event): void
    {
        $applicant = TypedValue::model($event->applicant->fresh(['convertedStudent', 'admissionPeriod']));

        app(AutomationRunLogger::class)->run(
            ApplicantAccepted::class,
            $applicant,
            function () use ($applicant, $event) {
                $student = $applicant->convertedStudent;

                if (! $student) {
                    $student = app(ApplicantPromotionService::class)
                        ->promote($applicant, $event->actor);
                }

                return app(LibraryMemberProvisioningService::class)->provisionFor($applicant, $student);
            },
            'create_library_member',
        );
    }
}
