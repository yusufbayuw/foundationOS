<?php

namespace Modules\Procurement\Filament\Resources\RfqItems\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;

class RfqItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('References'))
                    ->columns(2)
                    ->schema([
                        TenantField::make(),
                        Select::make('request_for_quotation_id')
                            ->label(FilamentUi::field('request_for_quotation_id'))
                            ->relationship('requestForQuotation', 'id')
                            ->required(),
                        Select::make('procurement_item_id')
                            ->label(FilamentUi::field('procurement_item_id'))
                            ->relationship('procurementItem', 'name'),
                        Textarea::make('description')
                            ->label(FilamentUi::field('description'))
                            ->columnSpanFull(),
                        Textarea::make('specifications')
                            ->label(FilamentUi::field('specifications'))
                            ->columnSpanFull(),
                    ]),

                Section::make(FilamentUi::text('Quantity & Budget'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('quantity')
                            ->label(FilamentUi::field('quantity'))
                            ->required()
                            ->numeric()
                            ->default(1),
                        TextInput::make('unit_of_measure')
                            ->label(FilamentUi::field('unit_of_measure')),
                        TextInput::make('estimated_budget')
                            ->label(FilamentUi::field('estimated_budget'))
                            ->required()
                            ->numeric()
                            ->default(0),
                    ]),

                Section::make(FilamentUi::text('Requirements & Criteria'))
                    ->columns(2)
                    ->schema([
                        Textarea::make('technical_requirements')
                            ->label(FilamentUi::field('technical_requirements'))
                            ->columnSpanFull(),
                        Textarea::make('mandatory_requirements')
                            ->label(FilamentUi::field('mandatory_requirements'))
                            ->columnSpanFull(),
                        Textarea::make('scoring_criteria')
                            ->label(FilamentUi::field('scoring_criteria'))
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
