<?php

namespace Modules\School\Listeners;

use App\Concerns\InteractsWithTenant;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Modules\Enrollment\Events\ApplicantAccepted;
use Modules\Enrollment\Services\ApplicantPromotionService;
use Modules\Monitoring\Services\AutomationRunLogger;

class CreateStudentFromAcceptedApplicant implements ShouldQueue
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
            fn () => app(ApplicantPromotionService::class)->promote($event->applicant, $event->actor),
            'create_student',
        );
    }
}
