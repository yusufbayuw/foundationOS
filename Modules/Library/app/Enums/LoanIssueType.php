<?php

namespace Modules\Library\Enums;

enum LoanIssueType: string
{
    case Lost = 'lost';
    case Damaged = 'damaged';

    public function label(): string
    {
        return match ($this) {
            self::Lost => 'hilang',
            self::Damaged => 'rusak',
        };
    }
}
