<?php

namespace Modules\Global\Filament\Resources\Timezones\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class TimezoneForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('Timezone Details'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('code')
                            ->label(FilamentUi::field('code'))
                            ->required(),
                        TextInput::make('name')
                            ->label(FilamentUi::field('name'))
                            ->required(),
                        TextInput::make('utc_offset')
                            ->label(FilamentUi::field('utc_offset')),
                    ]),
            ]);
    }
}
