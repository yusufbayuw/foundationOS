<?php

namespace Modules\Procurement\Filament\Resources\GoodsReceiptItems\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class GoodsReceiptItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('tenant_id'))
                    ->relationship('tenant', 'name')
                    ->required(),
                Select::make('goods_receipt_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('goods_receipt_id'))
                    ->relationship('goodsReceipt', 'id')
                    ->required(),
                Select::make('purchase_order_item_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('purchase_order_item_id'))
                    ->relationship('purchaseOrderItem', 'id'),
                TextInput::make('quantity_received')
                    ->label(\Modules\Core\Support\FilamentUi::field('quantity_received'))
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('quantity_accepted')
                    ->label(\Modules\Core\Support\FilamentUi::field('quantity_accepted'))
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('quantity_rejected')
                    ->label(\Modules\Core\Support\FilamentUi::field('quantity_rejected'))
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('unit_price')
                    ->label(\Modules\Core\Support\FilamentUi::field('unit_price'))
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->prefix('$'),
                TextInput::make('total_amount')
                    ->label(\Modules\Core\Support\FilamentUi::field('total_amount'))
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('condition_status')
                    ->label(\Modules\Core\Support\FilamentUi::field('condition_status'))
                    ->required()
                    ->default('good'),
                Textarea::make('notes')
                    ->label(\Modules\Core\Support\FilamentUi::field('notes'))
                    ->columnSpanFull(),
            ]);
    }
}
