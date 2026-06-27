<?php

namespace App\Observers;

use App\Integrations\Moodle\MoodleOutboxService;
use App\Integrations\Moodle\MoodleSyncContext;
use App\Support\TypedValue;
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

        if (MoodleSyncContext::disabled() || ! config('moodle.enabled', false)) {
            return;
        }

        CourseOffering::withoutTenantScope()
            ->where('academic_period_id', $period->id)
            ->where('tenant_id', $period->tenant_id)
            ->select('id', 'tenant_id', 'course_id', 'academic_period_id', 'class_code', 'status', 'updated_at', 'deleted_at')
            ->cursor()
            ->each(function (CourseOffering $offering) use ($period): void {
                $version = TypedValue::int(data_get($offering, 'updated_at.timestamp'), TypedValue::int(now()->timestamp));
                $dedupe = "academic_period:{$period->id}:offering:{$offering->id}:upsert:{$version}";

                $this->outbox->enqueue(
                    MoodleOutboxService::ENTITY_COURSE_OFFERING,
                    (int) $offering->id,
                    (int) $offering->tenant_id,
                    MoodleOutboxService::ACTION_UPSERT,
                    [
                        'tenant_id' => (int) $offering->tenant_id,
                        'course_id' => (int) $offering->course_id,
                        'academic_period_id' => (int) $period->id,
                        'class_code' => $offering->class_code,
                        'status' => $offering->status,
                        'trigger' => 'academic_period_updated',
                    ],
                    $dedupe,
                );
            });
    }
}
