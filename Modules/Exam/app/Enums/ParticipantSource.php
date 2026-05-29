<?php

namespace Modules\Exam\Enums;

enum ParticipantSource: string
{
    case SchoolStudent = 'school_student';
    case CampusStudent = 'campus_student';
    case User = 'user';
    case Manual = 'manual';
    case Import = 'import';

    public function label(): string
    {
        return match ($this) {
            self::SchoolStudent => 'School student',
            self::CampusStudent => 'Campus student',
            self::User => 'User',
            self::Manual => 'Manual',
            self::Import => 'Import',
        };
    }
}
