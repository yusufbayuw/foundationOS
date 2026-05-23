<?php

namespace Modules\Core\Enums;

enum OrganizationType: string
{
    case UnitPendidikan = 'unit_pendidikan';
    case UnitUsaha = 'unit_usaha';
    case Direktorat = 'direktorat';
    case Cabang = 'cabang';

    public function label(): string
    {
        return match ($this) {
            self::UnitPendidikan => 'Education unit',
            self::UnitUsaha => 'Business unit',
            self::Direktorat => 'Directorate',
            self::Cabang => 'Branch',
        };
    }
}
