<?php

namespace Modules\Global\Filament\Resources\Provinces\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProvinceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Province Details')
                    ->columns(2)
                    ->schema([
                        Select::make('country_id')
                            ->label(\Modules\Core\Support\FilamentUi::field('country_id'))
                            ->relationship('country', 'name')
                            ->required(),
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
