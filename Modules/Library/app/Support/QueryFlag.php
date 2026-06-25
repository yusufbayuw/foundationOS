<?php

namespace Modules\Library\Support;

class QueryFlag
{
    public static function isTruthy(mixed $value): bool
    {
        return in_array(strtolower((string) $value), ['1', 'true', 'yes', 'on'], true);
    }
}
