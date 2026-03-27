<?php

namespace Modules\Procurement\Filament\Resources\VendorBills\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;

class VendorBillForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TenantField::make(),
                Select::make('vendor_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('vendor_id'))
                    ->relationship('vendor', 'name')
                    ->required(),
                Select::make('purchase_order_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('purchase_order_id'))
                    ->relationship('purchaseOrder', 'id'),
                Select::make('goods_receipt_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('goods_receipt_id'))
                    ->relationship('goodsReceipt', 'id'),
                Select::make('journal_entry_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('journal_entry_id'))
                    ->relationship('journalEntry', 'id'),
                TextInput::make('processed_by')
                    ->label(\Modules\Core\Support\FilamentUi::field('processed_by'))
                    ->numeric(),
                TextInput::make('bill_number')
                    ->label(\Modules\Core\Support\FilamentUi::field('bill_number'))
                    ->required(),
                DatePicker::make('bill_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('bill_date'))
                    ->required(),
                DatePicker::make('due_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('due_date')),
                TextInput::make('reference_number')
                    ->label(\Modules\Core\Support\FilamentUi::field('reference_number')),
                TextInput::make('subtotal')
                    ->label(\Modules\Core\Support\FilamentUi::field('subtotal'))
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('discount_amount')
                    ->label(\Modules\Core\Support\FilamentUi::field('discount_amount'))
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('tax_amount')
                    ->label(\Modules\Core\Support\FilamentUi::field('tax_amount'))
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('other_costs')
                    ->label(\Modules\Core\Support\FilamentUi::field('other_costs'))
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('total_amount')
                    ->label(\Modules\Core\Support\FilamentUi::field('total_amount'))
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('amount_paid')
                    ->label(\Modules\Core\Support\FilamentUi::field('amount_paid'))
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('status')
                    ->label(\Modules\Core\Support\FilamentUi::field('status'))
                    ->required()
                    ->default('draft'),
                TextInput::make('payment_status')
                    ->label(\Modules\Core\Support\FilamentUi::field('payment_status'))
                    ->required()
                    ->default('unpaid'),
                Textarea::make('notes')
                    ->label(\Modules\Core\Support\FilamentUi::field('notes'))
                    ->columnSpanFull(),
                DateTimePicker::make('processed_at'),
            ]);
    }
}
