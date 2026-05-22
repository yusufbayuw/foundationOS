<?php

namespace Modules\Procurement\Filament\Resources\PurchaseOrders\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;

class PurchaseOrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('Order Information'))
                    ->columns(2)
                    ->schema([
                        TenantField::make(),
                        Select::make('request_for_quotation_id')
                            ->label(FilamentUi::field('request_for_quotation_id'))
                            ->relationship('requestForQuotation', 'id'),
                        Select::make('vendor_id')
                            ->label(FilamentUi::field('vendor_id'))
                            ->relationship('vendor', 'name')
                            ->required(),
                        TextInput::make('po_number')
                            ->label(FilamentUi::field('po_number'))
                            ->required(),
                        DatePicker::make('po_date')
                            ->label(FilamentUi::field('po_date'))
                            ->required(),
                        DatePicker::make('delivery_date')
                            ->label(FilamentUi::field('delivery_date')),
                        TextInput::make('delivery_location')
                            ->label(FilamentUi::field('delivery_location')),
                        TextInput::make('payment_terms')
                            ->label(FilamentUi::field('payment_terms')),
                    ]),

                Section::make(FilamentUi::text('Financial'))
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
                        TextInput::make('shipping_cost')
                            ->label(FilamentUi::field('shipping_cost'))
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->prefix('$'),
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
                        TextInput::make('currency')
                            ->label(FilamentUi::field('currency'))
                            ->required()
                            ->default('IDR'),
                        TextInput::make('exchange_rate')
                            ->label(FilamentUi::field('exchange_rate'))
                            ->required()
                            ->numeric()
                            ->default(1),
                    ]),

                Section::make(FilamentUi::text('Status & Approval'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('status')
                            ->label(FilamentUi::field('status'))
                            ->required()
                            ->default('draft'),
                        DateTimePicker::make('sent_at'),
                        TextInput::make('approved_by')
                            ->label(FilamentUi::field('approved_by'))
                            ->numeric(),
                        DateTimePicker::make('approved_at'),
                    ]),

                Section::make(FilamentUi::text('Terms & Notes'))
                    ->columns(1)
                    ->schema([
                        Textarea::make('notes')
                            ->label(FilamentUi::field('notes'))
                            ->columnSpanFull(),
                        Textarea::make('terms_conditions')
                            ->label(FilamentUi::field('terms_conditions'))
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
