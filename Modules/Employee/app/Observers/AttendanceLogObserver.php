<?php

namespace Modules\Employee\Observers;

use Modules\Employee\Models\AttendanceLog;
use Modules\Employee\Services\AttendanceProcessingService;

class AttendanceLogObserver
{
    public function __construct(private readonly AttendanceProcessingService $service) {}

    public function saved(AttendanceLog $log): void
    {
        // Recalculate work_hours when check_in or check_out changes
        if (
            $log->check_in && $log->check_out &&
            ($log->wasChanged('check_in') || $log->wasChanged('check_out') || $log->wasRecentlyCreated)
        ) {
            // Avoid recursive save loop — update directly
            AttendanceLog::withoutEvents(function () use ($log): void {
                $this->service->recalculate($log);
            });
        }
    }
}
