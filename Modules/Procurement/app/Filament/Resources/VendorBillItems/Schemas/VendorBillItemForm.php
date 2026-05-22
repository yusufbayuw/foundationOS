<?php

namespace Modules\Procurement\Filament\Resources\VendorBillItems\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;

class VendorBillItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('References'))
                    ->columns(2)
                    ->schema([
                        TenantField::make(),
                        Select::make('vendor_bill_id')
                            ->label(FilamentUi::field('vendor_bill_id'))
                            ->relationship('vendorBill', 'id')
                            ->required(),
                        Select::make('purchase_order_item_id')
                            ->label(FilamentUi::field('purchase_order_item_id'))
                            ->relationship('purchaseOrderItem', 'id'),
                        Select::make('goods_receipt_item_id')
                            ->label(FilamentUi::field('goods_receipt_item_id'))
                            ->relationship('goodsReceiptItem', 'id'),
                        Textarea::make('description')
                            ->label(FilamentUi::field('description'))
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
                        Textarea::make('notes')
                            ->label(FilamentUi::field('notes'))
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
