<?php

namespace App\Integrations\Moodle;

use App\Support\TypedValue;
use Illuminate\Support\Carbon;

class MoodleSyncRetry
{
    public function nextRetryAt(int $attempts): Carbon
    {
        /** @var list<int> $minutes */
        $minutes = TypedValue::intList(config('moodle.backoff_minutes'), [1, 2, 5, 10, 20, 30, 60]);
        $index = max(0, min($attempts - 1, count($minutes) - 1));
        $wait = $minutes[$index] ?? 60;

        return now()->addMinutes(max(1, $wait));
    }
}
