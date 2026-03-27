<?php

namespace Modules\Procurement\Filament\Resources\VendorBillItems\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;

class VendorBillItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('tenant_id'))
                    ->relationship('tenant', 'name')
                    ->required(),
                Select::make('vendor_bill_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('vendor_bill_id'))
                    ->relationship('vendorBill', 'id')
                    ->required(),
                Select::make('purchase_order_item_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('purchase_order_item_id'))
                    ->relationship('purchaseOrderItem', 'id'),
                Select::make('goods_receipt_item_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('goods_receipt_item_id'))
                    ->relationship('goodsReceiptItem', 'id'),
                Textarea::make('description')
                    ->label(\Modules\Core\Support\FilamentUi::field('description'))
                    ->columnSpanFull(),
                TextInput::make('quantity')
                    ->label(\Modules\Core\Support\FilamentUi::field('quantity'))
                    ->required()
                    ->numeric()
                    ->default(1),
                TextInput::make('unit_of_measure')
                    ->label(\Modules\Core\Support\FilamentUi::field('unit_of_measure')),
                TextInput::make('unit_price')
                    ->label(\Modules\Core\Support\FilamentUi::field('unit_price'))
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->prefix('$'),
                TextInput::make('tax_amount')
                    ->label(\Modules\Core\Support\FilamentUi::field('tax_amount'))
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('line_total')
                    ->label(\Modules\Core\Support\FilamentUi::field('line_total'))
                    ->required()
                    ->numeric()
                    ->default(0),
                Textarea::make('notes')
                    ->label(\Modules\Core\Support\FilamentUi::field('notes'))
                    ->columnSpanFull(),
            ]);
    }
}
