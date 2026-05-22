<?php

namespace Modules\Procurement\Filament\Resources\RfqItems\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class RfqItemInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('References'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('tenant.name')
                            ->label(FilamentUi::text('Tenant')),
                        TextEntry::make('requestForQuotation.id')
                            ->label(FilamentUi::text('Request for quotation')),
                        TextEntry::make('procurementItem.name')
                            ->label(FilamentUi::text('Procurement item'))
                            ->placeholder('-'),
                        TextEntry::make('description')
                            ->label(FilamentUi::field('description'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('specifications')
                            ->label(FilamentUi::field('specifications'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ]),

                Section::make(FilamentUi::text('Quantity & Budget'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('quantity')
                            ->label(FilamentUi::field('quantity'))
                            ->numeric(),
                        TextEntry::make('unit_of_measure')
                            ->label(FilamentUi::field('unit_of_measure'))
                            ->placeholder('-'),
                        TextEntry::make('estimated_budget')
                            ->label(FilamentUi::field('estimated_budget'))
                            ->numeric(),
                    ]),

                Section::make(FilamentUi::text('Requirements & Criteria'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('technical_requirements')
                            ->label(FilamentUi::field('technical_requirements'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('mandatory_requirements')
                            ->label(FilamentUi::field('mandatory_requirements'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('scoring_criteria')
                            ->label(FilamentUi::field('scoring_criteria'))
                            ->placeholder('-')
                            ->columnSpanFull(),
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
