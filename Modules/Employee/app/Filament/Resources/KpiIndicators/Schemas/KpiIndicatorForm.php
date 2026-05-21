<?php

namespace Modules\Employee\Filament\Resources\KpiIndicators\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;

class KpiIndicatorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('General Information')
                    ->columns(2)
                    ->schema([
                        TenantField::make(),
                        Select::make('organization_id')
                            ->label(FilamentUi::field('organization_id'))
                            ->relationship('organization', 'name')
                            ->required(),
                        TextInput::make('code')
                            ->label(FilamentUi::field('code'))
                            ->required(),
                        TextInput::make('name')
                            ->label(FilamentUi::field('name'))
                            ->required(),
                        TextInput::make('category')
                            ->label(FilamentUi::field('category')),
                        TextInput::make('measurement_unit')
                            ->label(FilamentUi::field('measurement_unit')),
                        Textarea::make('description')
                            ->label(FilamentUi::field('description'))
                            ->columnSpanFull(),
                    ]),

                Section::make('Target & Scoring')
                    ->columns(2)
                    ->schema([
                        TextInput::make('target_type')
                            ->label(FilamentUi::field('target_type')),
                        TextInput::make('target_value')
                            ->label(FilamentUi::field('target_value'))
                            ->numeric(),
                        TextInput::make('target_minimum')
                            ->label(FilamentUi::field('target_minimum'))
                            ->numeric(),
                        TextInput::make('target_maximum')
                            ->label(FilamentUi::field('target_maximum'))
                            ->numeric(),
                        TextInput::make('weight_percentage')
                            ->label(FilamentUi::field('weight_percentage'))
                            ->required()
                            ->numeric()
                            ->default(0),
                        TextInput::make('scoring_method')
                            ->label(FilamentUi::field('scoring_method')),
                        Textarea::make('formula')
                            ->label(FilamentUi::field('formula'))
                            ->columnSpanFull(),
                    ]),

                Section::make('Data & Status')
                    ->columns(2)
                    ->schema([
                        TextInput::make('data_source')
                            ->label(FilamentUi::field('data_source')),
                        Toggle::make('is_active')
                            ->label(FilamentUi::field('is_active'))
                            ->required(),
                    ]),
            ]);
    }
}
