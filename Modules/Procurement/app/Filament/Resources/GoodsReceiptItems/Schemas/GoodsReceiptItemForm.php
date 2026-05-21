<?php

namespace Modules\Procurement\Filament\Resources\GoodsReceiptItems\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;

class GoodsReceiptItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Reference')
                    ->columns(2)
                    ->schema([
                        TenantField::make(),
                        Select::make('goods_receipt_id')
                            ->label(FilamentUi::field('goods_receipt_id'))
                            ->relationship('goodsReceipt', 'id')
                            ->required(),
                        Select::make('purchase_order_item_id')
                            ->label(FilamentUi::field('purchase_order_item_id'))
                            ->relationship('purchaseOrderItem', 'id'),
                    ]),

                Section::make('Quantities')
                    ->columns(2)
                    ->schema([
                        TextInput::make('quantity_received')
                            ->label(FilamentUi::field('quantity_received'))
                            ->required()
                            ->numeric()
                            ->default(0),
                        TextInput::make('quantity_accepted')
                            ->label(FilamentUi::field('quantity_accepted'))
                            ->required()
                            ->numeric()
                            ->default(0),
                        TextInput::make('quantity_rejected')
                            ->label(FilamentUi::field('quantity_rejected'))
                            ->required()
                            ->numeric()
                            ->default(0),
                        TextInput::make('condition_status')
                            ->label(FilamentUi::field('condition_status'))
                            ->required()
                            ->default('good'),
                    ]),

                Section::make('Pricing')
                    ->columns(2)
                    ->schema([
                        TextInput::make('unit_price')
                            ->label(FilamentUi::field('unit_price'))
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->prefix('$'),
                        TextInput::make('total_amount')
                            ->label(FilamentUi::field('total_amount'))
                            ->required()
                            ->numeric()
                            ->default(0),
                    ]),

                Section::make('Notes')
                    ->columns(1)
                    ->schema([
                        Textarea::make('notes')
                            ->label(FilamentUi::field('notes'))
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
