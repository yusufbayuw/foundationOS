<?php

namespace Modules\Exam\Enums;

enum ParticipantStatus: string
{
    case Assigned = 'assigned';
    case Started = 'started';
    case Submitted = 'submitted';
    case Absent = 'absent';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Assigned => 'Assigned',
            self::Started => 'Started',
            self::Submitted => 'Submitted',
            self::Absent => 'Absent',
            self::Cancelled => 'Cancelled',
        };
    }
}
