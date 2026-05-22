<?php

namespace Modules\Employee\Filament\Resources\KpiIndicators\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class KpiIndicatorInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('General Information'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('tenant.name')
                            ->label(FilamentUi::text('Tenant')),
                        TextEntry::make('organization.name')
                            ->label(FilamentUi::text('Organization')),
                        TextEntry::make('code')
                            ->label(FilamentUi::field('code')),
                        TextEntry::make('name')
                            ->label(FilamentUi::field('name')),
                        TextEntry::make('category')
                            ->label(FilamentUi::field('category'))
                            ->placeholder('-'),
                        TextEntry::make('measurement_unit')
                            ->label(FilamentUi::field('measurement_unit'))
                            ->placeholder('-'),
                        TextEntry::make('description')
                            ->label(FilamentUi::field('description'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ]),

                Section::make(FilamentUi::text('Target & Scoring'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('target_type')
                            ->label(FilamentUi::field('target_type'))
                            ->placeholder('-'),
                        TextEntry::make('target_value')
                            ->label(FilamentUi::field('target_value'))
                            ->numeric()
                            ->placeholder('-'),
                        TextEntry::make('target_minimum')
                            ->label(FilamentUi::field('target_minimum'))
                            ->numeric()
                            ->placeholder('-'),
                        TextEntry::make('target_maximum')
                            ->label(FilamentUi::field('target_maximum'))
                            ->numeric()
                            ->placeholder('-'),
                        TextEntry::make('weight_percentage')
                            ->label(FilamentUi::field('weight_percentage'))
                            ->numeric(),
                        TextEntry::make('scoring_method')
                            ->label(FilamentUi::field('scoring_method'))
                            ->placeholder('-'),
                        TextEntry::make('formula')
                            ->label(FilamentUi::field('formula'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ]),

                Section::make(FilamentUi::text('Data & Status'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('data_source')
                            ->label(FilamentUi::field('data_source'))
                            ->placeholder('-'),
                        IconEntry::make('is_active')
                            ->boolean(),
                    ]),

                Section::make(FilamentUi::text('Timestamps'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('created_at')
                            ->label(FilamentUi::field('created_at'))
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('updated_at')
                            ->label(FilamentUi::field('updated_at'))
                            ->dateTime()
                            ->placeholder('-'),
                    ]),
            ]);
    }
}
