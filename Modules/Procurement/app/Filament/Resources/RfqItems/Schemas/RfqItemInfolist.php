<?php

namespace Modules\Procurement\Filament\Resources\RfqItems\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class RfqItemInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('References')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('tenant.name')
                            ->label(\Modules\Core\Support\FilamentUi::text('Tenant')),
                        TextEntry::make('requestForQuotation.id')
                            ->label(\Modules\Core\Support\FilamentUi::text('Request for quotation')),
                        TextEntry::make('procurementItem.name')
                            ->label(\Modules\Core\Support\FilamentUi::text('Procurement item'))
                            ->placeholder('-'),
                        TextEntry::make('description')
                            ->label(\Modules\Core\Support\FilamentUi::field('description'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('specifications')
                            ->label(\Modules\Core\Support\FilamentUi::field('specifications'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ]),

                Section::make('Quantity & Budget')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('quantity')
                            ->label(\Modules\Core\Support\FilamentUi::field('quantity'))
                            ->numeric(),
                        TextEntry::make('unit_of_measure')
                            ->label(\Modules\Core\Support\FilamentUi::field('unit_of_measure'))
                            ->placeholder('-'),
                        TextEntry::make('estimated_budget')
                            ->label(\Modules\Core\Support\FilamentUi::field('estimated_budget'))
                            ->numeric(),
                    ]),

                Section::make('Requirements & Criteria')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('technical_requirements')
                            ->label(\Modules\Core\Support\FilamentUi::field('technical_requirements'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('mandatory_requirements')
                            ->label(\Modules\Core\Support\FilamentUi::field('mandatory_requirements'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('scoring_criteria')
                            ->label(\Modules\Core\Support\FilamentUi::field('scoring_criteria'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ]),

                Section::make('Timestamps')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('created_at')
                            ->label(\Modules\Core\Support\FilamentUi::field('created_at'))
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('updated_at')
                            ->label(\Modules\Core\Support\FilamentUi::field('updated_at'))
                            ->dateTime()
                            ->placeholder('-'),
                    ]),
            ]);
    }
}
