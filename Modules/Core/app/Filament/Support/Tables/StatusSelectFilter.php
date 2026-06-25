<?php

namespace Modules\Core\Filament\Support\Tables;

use Filament\Tables\Filters\SelectFilter;
use Modules\Core\Support\FilamentUi;

class StatusSelectFilter
{
    /**
     * @param  array<string, string>  $valueToEnglishLabel  map of DB value => English phrase for FilamentUi
     */
    public static function make(array $valueToEnglishLabel, string $name = 'status'): SelectFilter
    {
        $options = [];

        foreach ($valueToEnglishLabel as $value => $englishLabel) {
            $options[$value] = FilamentUi::text($englishLabel);
        }

        return SelectFilter::make($name)
            ->label(FilamentUi::field($name))
            ->options($options);
    }
}
