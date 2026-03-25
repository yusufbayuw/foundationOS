<?php

namespace Modules\Core\Filament\Support;

use BackedEnum;
use Filament\Resources\Resource;
use Illuminate\Contracts\Support\Htmlable;
use Modules\Core\Support\FilamentUi;
use UnitEnum;

abstract class ModuleResource extends Resource
{
    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return FilamentUi::module(static::getModuleName());
    }

    public static function getNavigationIcon(): string|BackedEnum|Htmlable|null
    {
        return FilamentUi::resourceIcon(static::getModel());
    }

    public static function getNavigationLabel(): string
    {
        return FilamentUi::text(parent::getPluralModelLabel());
    }

    public static function getModelLabel(): string
    {
        return FilamentUi::resource(static::getModel());
    }

    public static function getPluralModelLabel(): string
    {
        return FilamentUi::text(parent::getPluralModelLabel());
    }

    protected static function getModuleName(): string
    {
        return str(static::class)->after('Modules\\')->before('\\Filament\\Resources')->beforeLast('\\')->toString();
    }
}
