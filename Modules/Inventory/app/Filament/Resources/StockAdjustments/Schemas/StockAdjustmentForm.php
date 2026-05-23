<?php

namespace Modules\Inventory\Filament\Resources\StockAdjustments\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;
use Modules\Inventory\Enums\StockAdjustmentReason;

class StockAdjustmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(FilamentUi::text('General information'))
                ->columns(2)
                ->schema([
                    TenantField::make(),
                    Select::make('organization_id')
                        ->label(FilamentUi::field('organization_id'))
                        ->relationship('organization', 'name'),
                    Select::make('warehouse_id')
                        ->label(FilamentUi::field('warehouse_id'))
                        ->relationship('warehouse', 'name')
                        ->required(),
                    TextInput::make('adjustment_number')
                        ->label(FilamentUi::field('adjustment_number'))
                        ->required(),
                    Select::make('reason')
                        ->label(FilamentUi::field('reason'))
                        ->options(collect(StockAdjustmentReason::cases())->mapWithKeys(
                            fn (StockAdjustmentReason $r) => [$r->value => $r->label()],
                        )->all())
                        ->required(),
                    Textarea::make('notes')
                        ->label(FilamentUi::field('notes'))
                        ->columnSpanFull(),
                ]),
            Section::make(FilamentUi::text('Adjustment lines'))
                ->schema([
                    Repeater::make('lines')
                        ->relationship()
                        ->schema([
                            Select::make('stock_item_id')
                                ->label(FilamentUi::field('stock_item_id'))
                                ->relationship('stockItem', 'name')
                                ->required(),
                            TextInput::make('quantity_delta')
                                ->label(FilamentUi::field('quantity_delta'))
                                ->numeric()
                                ->required()
                                ->helperText(FilamentUi::text('Use negative quantity for stock reduction.')),
                            TextInput::make('unit_cost')
                                ->label(FilamentUi::field('unit_cost'))
                                ->numeric()
                                ->default(0),
                        ])
                        ->columns(3)
                        ->columnSpanFull(),
                ]),
        ]);
    }
}
