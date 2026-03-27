<?php

namespace App\Integrations\Moodle;

class MoodleSyncRetry
{
    public function nextRetryAt(int $attempts): \Illuminate\Support\Carbon
    {
        $minutes = config('moodle.backoff_minutes', [1, 2, 5, 10, 20, 30, 60]);
        $index = max(0, min($attempts - 1, count($minutes) - 1));
        $wait = (int) ($minutes[$index] ?? 60);

        return now()->addMinutes(max(1, $wait));
    }
}

