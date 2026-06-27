<?php

namespace Modules\School\Support;

final class AttendanceRecapRow
{
    public function __construct(
        public int|string $student_id,
        public ?string $nis,
        public string $student_name,
        public int $total_present,
        public int $total_absent,
        public int $total_sick,
        public int $total_permission,
        public int $total_late,
        public int $total_days,
        public float $percentage,
    ) {}
}
