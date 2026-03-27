<?php

namespace Modules\Procurement\Filament\Resources\PurchaseOrderItems\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;

class PurchaseOrderItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TenantField::make(),
                Select::make('purchase_order_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('purchase_order_id'))
                    ->relationship('purchaseOrder', 'id')
                    ->required(),
                Select::make('purchase_requisition_item_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('purchase_requisition_item_id'))
                    ->relationship('purchaseRequisitionItem', 'id'),
                Select::make('procurement_item_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('procurement_item_id'))
                    ->relationship('procurementItem', 'name'),
                Textarea::make('description')
                    ->label(\Modules\Core\Support\FilamentUi::field('description'))
                    ->columnSpanFull(),
                Textarea::make('specifications')
                    ->label(\Modules\Core\Support\FilamentUi::field('specifications'))
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
                TextInput::make('discount_amount')
                    ->label(\Modules\Core\Support\FilamentUi::field('discount_amount'))
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('tax_percentage')
                    ->label(\Modules\Core\Support\FilamentUi::field('tax_percentage'))
                    ->required()
                    ->numeric()
                    ->default(0),
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
                TextInput::make('quantity_received')
                    ->label(\Modules\Core\Support\FilamentUi::field('quantity_received'))
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('status')
                    ->label(\Modules\Core\Support\FilamentUi::field('status'))
                    ->required()
                    ->default('open'),
                Textarea::make('notes')
                    ->label(\Modules\Core\Support\FilamentUi::field('notes'))
                    ->columnSpanFull(),
            ]);
    }
}
