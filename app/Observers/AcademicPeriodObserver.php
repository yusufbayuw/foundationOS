<?php

namespace App\Observers;

use App\Integrations\Moodle\MoodleOutboxService;
use Modules\Campus\Models\CourseOffering;
use Modules\Core\Models\AcademicPeriod;

class AcademicPeriodObserver
{
    public function __construct(protected MoodleOutboxService $outbox) {}

    public function updated(AcademicPeriod $period): void
    {
        if (! $period->wasChanged(['start_date', 'end_date', 'name'])) {
            return;
        }

        if (! config('moodle.enabled', false)) {
            return;
        }

        CourseOffering::withoutTenantScope()
            ->where('academic_period_id', $period->id)
            ->where('tenant_id', $period->tenant_id)
            ->select('id', 'tenant_id')
            ->cursor()
            ->each(function (CourseOffering $offering) {
                $this->outbox->enqueue(
                    MoodleOutboxService::ENTITY_COURSE_OFFERING,
                    (int) $offering->id,
                    (int) $offering->tenant_id,
                    MoodleOutboxService::ACTION_UPSERT,
                );
            });
    }
}
