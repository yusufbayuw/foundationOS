<?php

namespace Modules\Procurement\Filament\Resources\GoodsReceipts\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class GoodsReceiptForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('tenant_id'))
                    ->relationship('tenant', 'name')
                    ->required(),
                Select::make('purchase_order_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('purchase_order_id'))
                    ->relationship('purchaseOrder', 'id'),
                TextInput::make('received_by')
                    ->label(\Modules\Core\Support\FilamentUi::field('received_by'))
                    ->numeric(),
                TextInput::make('inspected_by')
                    ->label(\Modules\Core\Support\FilamentUi::field('inspected_by'))
                    ->numeric(),
                TextInput::make('receipt_number')
                    ->label(\Modules\Core\Support\FilamentUi::field('receipt_number'))
                    ->required(),
                DatePicker::make('receipt_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('receipt_date'))
                    ->required(),
                TextInput::make('delivery_note_number')
                    ->label(\Modules\Core\Support\FilamentUi::field('delivery_note_number')),
                TextInput::make('supplier_delivery_number')
                    ->label(\Modules\Core\Support\FilamentUi::field('supplier_delivery_number')),
                TextInput::make('status')
                    ->label(\Modules\Core\Support\FilamentUi::field('status'))
                    ->required()
                    ->default('draft'),
                Textarea::make('notes')
                    ->label(\Modules\Core\Support\FilamentUi::field('notes'))
                    ->columnSpanFull(),
                Textarea::make('inspection_notes')
                    ->label(\Modules\Core\Support\FilamentUi::field('inspection_notes'))
                    ->columnSpanFull(),
                DateTimePicker::make('received_at'),
            ]);
    }
}
