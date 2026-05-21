<?php

namespace Modules\Campus\Enums;

enum CourseOfferingLecturerRole: string
{
    case Primary = 'primary';
    case Assistant = 'assistant';

    public function moodleShortname(): string
    {
        return match ($this) {
            self::Primary => 'editingteacher',
            self::Assistant => 'teacher',
        };
    }

    public function moodleRoleId(): int
    {
        return match ($this) {
            self::Primary => (int) config('moodle.role_map.teacher', 3),
            self::Assistant => (int) config('moodle.role_map.assistant_teacher', 4),
        };
    }
}
