<?php

namespace Modules\Sales\Filament\Resources\SalesOrders\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;

class SalesOrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(FilamentUi::text('General information'))
                ->schema([
                    TenantField::make(),
                    TenantField::organizationSelect(),
                    TextInput::make('customer_id')
                        ->label(FilamentUi::field('customer_id'))
                        ->numeric(),
                    TextInput::make('order_number')
                        ->label(FilamentUi::field('order_number'))
                        ->numeric(),
                    TextInput::make('order_date')
                        ->label(FilamentUi::field('order_date'))
                        ->numeric(),
                    TextInput::make('status')
                        ->label(FilamentUi::field('status')),
                    TextInput::make('subtotal')
                        ->label(FilamentUi::field('subtotal'))
                        ->numeric(),
                    TextInput::make('tax_amount')
                        ->label(FilamentUi::field('tax_amount'))
                        ->numeric(),
                    TextInput::make('total_amount')
                        ->label(FilamentUi::field('total_amount'))
                        ->numeric(),
                    TextInput::make('confirmed_at')
                        ->label(FilamentUi::field('confirmed_at')),
                ])
                ->columns(2),
        ]);
    }
}
