<?php

namespace Modules\Procurement\Filament\Resources\PurchaseOrders\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;

class PurchaseOrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('tenant_id'))
                    ->relationship('tenant', 'name')
                    ->required(),
                Select::make('request_for_quotation_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('request_for_quotation_id'))
                    ->relationship('requestForQuotation', 'id'),
                Select::make('vendor_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('vendor_id'))
                    ->relationship('vendor', 'name')
                    ->required(),
                TextInput::make('approved_by')
                    ->label(\Modules\Core\Support\FilamentUi::field('approved_by'))
                    ->numeric(),
                TextInput::make('po_number')
                    ->label(\Modules\Core\Support\FilamentUi::field('po_number'))
                    ->required(),
                DatePicker::make('po_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('po_date'))
                    ->required(),
                DatePicker::make('delivery_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('delivery_date')),
                TextInput::make('delivery_location')
                    ->label(\Modules\Core\Support\FilamentUi::field('delivery_location')),
                TextInput::make('payment_terms')
                    ->label(\Modules\Core\Support\FilamentUi::field('payment_terms')),
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
                TextInput::make('shipping_cost')
                    ->label(\Modules\Core\Support\FilamentUi::field('shipping_cost'))
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->prefix('$'),
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
                TextInput::make('currency')
                    ->label(\Modules\Core\Support\FilamentUi::field('currency'))
                    ->required()
                    ->default('IDR'),
                TextInput::make('exchange_rate')
                    ->label(\Modules\Core\Support\FilamentUi::field('exchange_rate'))
                    ->required()
                    ->numeric()
                    ->default(1),
                TextInput::make('status')
                    ->label(\Modules\Core\Support\FilamentUi::field('status'))
                    ->required()
                    ->default('draft'),
                DateTimePicker::make('sent_at'),
                Textarea::make('notes')
                    ->label(\Modules\Core\Support\FilamentUi::field('notes'))
                    ->columnSpanFull(),
                Textarea::make('terms_conditions')
                    ->label(\Modules\Core\Support\FilamentUi::field('terms_conditions'))
                    ->columnSpanFull(),
                DateTimePicker::make('approved_at'),
            ]);
    }
}
