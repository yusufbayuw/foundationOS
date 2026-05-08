<?php

namespace Modules\Employee\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum SalarySlipStatus: string implements HasColor, HasIcon, HasLabel
{
    case Draft = 'draft';
    case Processed = 'processed';
    case Paid = 'paid';
    case Cancelled = 'cancelled';

    public function getLabel(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Processed => 'Diproses',
            self::Paid => 'Dibayar',
            self::Cancelled => 'Dibatalkan',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Draft => 'gray',
            self::Processed => 'warning',
            self::Paid => 'success',
            self::Cancelled => 'danger',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::Draft => 'heroicon-o-document',
            self::Processed => 'heroicon-o-clock',
            self::Paid => 'heroicon-o-check-circle',
            self::Cancelled => 'heroicon-o-x-circle',
        };
    }

    public function isLockedForMutation(): bool
    {
        return in_array($this, [self::Paid, self::Cancelled]);
    }
}
