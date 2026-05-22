<?php

namespace Modules\Procurement\Filament\Resources\PurchaseOrderItems\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;

class PurchaseOrderItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('References'))
                    ->columns(2)
                    ->schema([
                        TenantField::make(),
                        Select::make('purchase_order_id')
                            ->label(FilamentUi::field('purchase_order_id'))
                            ->relationship('purchaseOrder', 'id')
                            ->required(),
                        Select::make('purchase_requisition_item_id')
                            ->label(FilamentUi::field('purchase_requisition_item_id'))
                            ->relationship('purchaseRequisitionItem', 'id'),
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

                Section::make(FilamentUi::text('Quantity & Pricing'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('quantity')
                            ->label(FilamentUi::field('quantity'))
                            ->required()
                            ->numeric()
                            ->default(1),
                        TextInput::make('unit_of_measure')
                            ->label(FilamentUi::field('unit_of_measure')),
                        TextInput::make('unit_price')
                            ->label(FilamentUi::field('unit_price'))
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->prefix('$'),
                        TextInput::make('discount_amount')
                            ->label(FilamentUi::field('discount_amount'))
                            ->required()
                            ->numeric()
                            ->default(0),
                        TextInput::make('tax_percentage')
                            ->label(FilamentUi::field('tax_percentage'))
                            ->required()
                            ->numeric()
                            ->default(0),
                        TextInput::make('tax_amount')
                            ->label(FilamentUi::field('tax_amount'))
                            ->required()
                            ->numeric()
                            ->default(0),
                        TextInput::make('line_total')
                            ->label(FilamentUi::field('line_total'))
                            ->required()
                            ->numeric()
                            ->default(0),
                    ]),

                Section::make(FilamentUi::text('Fulfillment & Status'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('quantity_received')
                            ->label(FilamentUi::field('quantity_received'))
                            ->required()
                            ->numeric()
                            ->default(0),
                        TextInput::make('status')
                            ->label(FilamentUi::field('status'))
                            ->required()
                            ->default('open'),
                        Textarea::make('notes')
                            ->label(FilamentUi::field('notes'))
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
