<?php

namespace Modules\Core\Filament\Resources\OrganizationSettings\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class OrganizationSettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic Info')
                    ->columns(2)
                    ->schema([
                        Select::make('organization_id')
                            ->label(FilamentUi::field('organization_id'))
                            ->relationship('organization', 'name')
                            ->required(),
                        TextInput::make('group')
                            ->label(FilamentUi::field('group')),
                        TextInput::make('key')
                            ->label(FilamentUi::field('key'))
                            ->required(),
                        TextInput::make('type')
                            ->label(FilamentUi::field('type'))
                            ->required()
                            ->default('string'),
                    ]),

                Section::make('Value')
                    ->columns(2)
                    ->schema([
                        Textarea::make('value')
                            ->label(FilamentUi::field('value'))
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
