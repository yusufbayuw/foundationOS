<?php

namespace Modules\Procurement\Filament\Resources\VendorBills\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;

class VendorBillForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('References')
                    ->columns(2)
                    ->schema([
                        TenantField::make(),
                        Select::make('vendor_id')
                            ->label(FilamentUi::field('vendor_id'))
                            ->relationship('vendor', 'name')
                            ->required(),
                        Select::make('purchase_order_id')
                            ->label(FilamentUi::field('purchase_order_id'))
                            ->relationship('purchaseOrder', 'id'),
                        Select::make('goods_receipt_id')
                            ->label(FilamentUi::field('goods_receipt_id'))
                            ->relationship('goodsReceipt', 'id'),
                        Select::make('journal_entry_id')
                            ->label(FilamentUi::field('journal_entry_id'))
                            ->relationship('journalEntry', 'id'),
                        TextInput::make('processed_by')
                            ->label(FilamentUi::field('processed_by'))
                            ->numeric(),
                    ]),

                Section::make('Bill Details')
                    ->columns(2)
                    ->schema([
                        TextInput::make('bill_number')
                            ->label(FilamentUi::field('bill_number'))
                            ->required(),
                        TextInput::make('reference_number')
                            ->label(FilamentUi::field('reference_number')),
                        DatePicker::make('bill_date')
                            ->label(FilamentUi::field('bill_date'))
                            ->required(),
                        DatePicker::make('due_date')
                            ->label(FilamentUi::field('due_date')),
                    ]),

                Section::make('Financial Summary')
                    ->columns(2)
                    ->schema([
                        TextInput::make('subtotal')
                            ->label(FilamentUi::field('subtotal'))
                            ->required()
                            ->numeric()
                            ->default(0),
                        TextInput::make('discount_amount')
                            ->label(FilamentUi::field('discount_amount'))
                            ->required()
                            ->numeric()
                            ->default(0),
                        TextInput::make('tax_amount')
                            ->label(FilamentUi::field('tax_amount'))
                            ->required()
                            ->numeric()
                            ->default(0),
                        TextInput::make('other_costs')
                            ->label(FilamentUi::field('other_costs'))
                            ->required()
                            ->numeric()
                            ->default(0),
                        TextInput::make('total_amount')
                            ->label(FilamentUi::field('total_amount'))
                            ->required()
                            ->numeric()
                            ->default(0),
                        TextInput::make('amount_paid')
                            ->label(FilamentUi::field('amount_paid'))
                            ->required()
                            ->numeric()
                            ->default(0),
                    ]),

                Section::make('Status & Notes')
                    ->columns(2)
                    ->schema([
                        TextInput::make('status')
                            ->label(FilamentUi::field('status'))
                            ->required()
                            ->default('draft'),
                        TextInput::make('payment_status')
                            ->label(FilamentUi::field('payment_status'))
                            ->required()
                            ->default('unpaid'),
                        DateTimePicker::make('processed_at'),
                        Textarea::make('notes')
                            ->label(FilamentUi::field('notes'))
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
