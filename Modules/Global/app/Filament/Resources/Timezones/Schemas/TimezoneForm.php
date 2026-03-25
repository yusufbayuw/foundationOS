<?php

namespace Modules\Global\Filament\Resources\Timezones\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TimezoneForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->label(\Modules\Core\Support\FilamentUi::field('code'))
                    ->required(),
                TextInput::make('name')
                    ->label(\Modules\Core\Support\FilamentUi::field('name'))
                    ->required(),
                TextInput::make('utc_offset')
                    ->label(\Modules\Core\Support\FilamentUi::field('utc_offset'))
            ]);
    }
}
