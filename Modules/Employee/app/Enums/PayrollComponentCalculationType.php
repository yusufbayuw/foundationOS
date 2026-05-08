<?php

namespace Modules\Employee\Enums;

use Filament\Support\Contracts\HasLabel;

enum PayrollComponentCalculationType: string implements HasLabel
{
    case Fixed = 'fixed';
    case Percentage = 'percentage';
    case Formula = 'formula';

    public function getLabel(): string
    {
        return match ($this) {
            self::Fixed => 'Nominal Tetap',
            self::Percentage => 'Persentase dari Gaji Pokok',
            self::Formula => 'Formula Kustom',
        };
    }
}
