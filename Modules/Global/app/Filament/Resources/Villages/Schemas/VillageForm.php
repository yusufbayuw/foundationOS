<?php

namespace Modules\Global\Filament\Resources\Villages\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class VillageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Village Details')
                    ->columns(2)
                    ->schema([
                        Select::make('district_id')
                            ->label(\Modules\Core\Support\FilamentUi::field('district_id'))
                            ->relationship('district', 'name')
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
