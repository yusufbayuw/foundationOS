<?php

namespace Modules\Employee\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum KpiScoreStatus: string implements HasColor, HasIcon, HasLabel
{
    case Draft = 'draft';
    case Submitted = 'submitted';
    case Evaluated = 'evaluated';
    case Approved = 'approved';

    public function getLabel(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Submitted => 'Diajukan',
            self::Evaluated => 'Dievaluasi',
            self::Approved => 'Disetujui',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Draft => 'gray',
            self::Submitted => 'info',
            self::Evaluated => 'warning',
            self::Approved => 'success',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::Draft => 'heroicon-o-document',
            self::Submitted => 'heroicon-o-paper-airplane',
            self::Evaluated => 'heroicon-o-clipboard-document-check',
            self::Approved => 'heroicon-o-check-circle',
        };
    }

    public function isLockedForMutation(): bool
    {
        return $this === self::Approved;
    }
}
