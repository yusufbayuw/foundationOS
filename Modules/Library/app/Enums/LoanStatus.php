<?php

namespace Modules\Library\Enums;

enum LoanStatus: string
{
    case Borrowed = 'borrowed';
    case Overdue = 'overdue';
    case Returned = 'returned';
    case Lost = 'lost';
    case Damaged = 'damaged';

    /**
     * @return list<string>
     */
    public static function activeValues(): array
    {
        return [self::Borrowed->value, self::Overdue->value];
    }

    /**
     * @return list<string>
     */
    public static function printableValues(): array
    {
        return [self::Borrowed->value, self::Overdue->value, self::Returned->value];
    }
}
