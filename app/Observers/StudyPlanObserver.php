<?php

namespace App\Observers;

use App\Integrations\Moodle\MoodleStudyPlanSyncService;
use App\Integrations\Moodle\MoodleSyncContext;
use Modules\Campus\Models\StudyPlan;

class StudyPlanObserver
{
    public function __construct(protected MoodleStudyPlanSyncService $sync) {}

    public function updated(StudyPlan $plan): void
    {
        if (MoodleSyncContext::disabled() || ! $plan->wasChanged('status')) {
            return;
        }

        $status = (string) $plan->status;

        if ($status === 'approved') {
            $this->sync->enrollAllForPlan($plan);

            return;
        }

        if (in_array($status, ['cancelled', 'revoked', 'rejected'], true)) {
            $this->sync->unenrollAllForPlan($plan);
        }
    }

    public function deleted(StudyPlan $plan): void
    {
        if (MoodleSyncContext::disabled()) {
            return;
        }

        $this->sync->unenrollAllForPlan($plan);
    }
}
