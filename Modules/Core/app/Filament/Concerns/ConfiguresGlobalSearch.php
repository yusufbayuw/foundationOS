<?php

namespace Modules\Core\Filament\Concerns;

use Illuminate\Database\Eloquent\Model;
use Modules\Core\Support\FilamentUi;

trait ConfiguresGlobalSearch
{
    /**
     * @return array<int, string>
     */
    public static function getGloballySearchableAttributes(): array
    {
        return static::globalSearchAttributes();
    }

    /**
     * @return array<string, string>
     */
    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return static::globalSearchResultDetails($record);
    }

    /**
     * @return array<int, string>
     */
    protected static function globalSearchAttributes(): array
    {
        return [];
    }

    /**
     * @return array<string, string>
     */
    protected static function globalSearchResultDetails(Model $record): array
    {
        return [];
    }

    /**
     * @return array<string, string>
     */
    protected static function detailStatus(?string $status): array
    {
        if ($status === null || $status === '') {
            return [];
        }

        return [FilamentUi::field('status') => $status];
    }
}
