<?php

namespace Modules\Global\Filament\Resources\Countries\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CountryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Country Details')
                    ->columns(2)
                    ->schema([
                        TextInput::make('code')
                            ->label(\Modules\Core\Support\FilamentUi::field('code'))
                            ->required(),
                        TextInput::make('name')
                            ->label(\Modules\Core\Support\FilamentUi::field('name'))
                            ->required(),
                    ]),
            ]);
    }
}
