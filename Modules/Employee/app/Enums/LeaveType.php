<?php

namespace Modules\Employee\Enums;

use Filament\Support\Contracts\HasLabel;

enum LeaveType: string implements HasLabel
{
    case Annual = 'annual';
    case Sick = 'sick';
    case Maternity = 'maternity';
    case Paternity = 'paternity';
    case Emergency = 'emergency';
    case Unpaid = 'unpaid';
    case Other = 'other';

    public function getLabel(): string
    {
        return match ($this) {
            self::Annual => 'Cuti Tahunan',
            self::Sick => 'Sakit',
            self::Maternity => 'Cuti Melahirkan',
            self::Paternity => 'Cuti Ayah',
            self::Emergency => 'Cuti Darurat',
            self::Unpaid => 'Cuti Tanpa Upah',
            self::Other => 'Lainnya',
        };
    }

    public function isPaid(): bool
    {
        return !in_array($this, [self::Unpaid]);
    }
}
