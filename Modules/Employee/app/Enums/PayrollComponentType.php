<?php

namespace Modules\Employee\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum PayrollComponentType: string implements HasColor, HasLabel
{
    case Earning = 'earning';
    case Deduction = 'deduction';
    case Tax = 'tax';
    case Bpjs = 'bpjs';

    public function getLabel(): string
    {
        return match ($this) {
            self::Earning => 'Pendapatan',
            self::Deduction => 'Potongan',
            self::Tax => 'Pajak (PPh 21)',
            self::Bpjs => 'BPJS',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Earning => 'success',
            self::Deduction => 'danger',
            self::Tax => 'warning',
            self::Bpjs => 'info',
        };
    }

    public function isDeduction(): bool
    {
        return in_array($this, [self::Deduction, self::Tax, self::Bpjs]);
    }
}
