<?php

namespace App\Integrations\Moodle;

class MoodleSyncContext
{
    protected static bool $disabled = false;

    public static function disabled(): bool
    {
        return static::$disabled;
    }

    public static function withoutObservers(callable $callback): mixed
    {
        $previous = static::$disabled;
        static::$disabled = true;

        try {
            return $callback();
        } finally {
            static::$disabled = $previous;
        }
    }
}
