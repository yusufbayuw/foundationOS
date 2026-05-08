<?php

namespace Modules\Employee\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum AttendanceStatus: string implements HasColor, HasIcon, HasLabel
{
    case Present = 'present';
    case Absent = 'absent';
    case Late = 'late';
    case EarlyLeave = 'early_leave';
    case Leave = 'leave';
    case Sick = 'sick';
    case Holiday = 'holiday';
    case Other = 'other';

    public function getLabel(): string
    {
        return match ($this) {
            self::Present => 'Hadir',
            self::Absent => 'Tidak Hadir',
            self::Late => 'Terlambat',
            self::EarlyLeave => 'Pulang Lebih Awal',
            self::Leave => 'Cuti',
            self::Sick => 'Sakit',
            self::Holiday => 'Libur',
            self::Other => 'Lainnya',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Present => 'success',
            self::Absent => 'danger',
            self::Late => 'warning',
            self::EarlyLeave => 'warning',
            self::Leave => 'info',
            self::Sick => 'info',
            self::Holiday => 'gray',
            self::Other => 'gray',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::Present => 'heroicon-o-check-circle',
            self::Absent => 'heroicon-o-x-circle',
            self::Late => 'heroicon-o-clock',
            self::EarlyLeave => 'heroicon-o-arrow-left-circle',
            self::Leave => 'heroicon-o-calendar-days',
            self::Sick => 'heroicon-o-heart',
            self::Holiday => 'heroicon-o-sun',
            self::Other => 'heroicon-o-ellipsis-horizontal-circle',
        };
    }

    public function countsAsAbsent(): bool
    {
        return in_array($this, [self::Absent]);
    }

    public function countsAsLeave(): bool
    {
        return in_array($this, [self::Leave, self::Sick]);
    }
}
