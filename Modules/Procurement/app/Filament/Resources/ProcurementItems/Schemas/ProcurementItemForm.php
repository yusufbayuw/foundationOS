<?php

namespace Modules\Procurement\Filament\Resources\ProcurementItems\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;

class ProcurementItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('Item Information'))
                    ->columns(2)
                    ->schema([
                        TenantField::make(),
                        Select::make('category_id')
                            ->label(FilamentUi::field('category_id'))
                            ->relationship('category', 'name'),
                        Select::make('preferred_vendor_id')
                            ->label(FilamentUi::field('preferred_vendor_id'))
                            ->relationship('preferredVendor', 'name'),
                        Select::make('chart_of_account_id')
                            ->label(FilamentUi::field('chart_of_account_id'))
                            ->relationship('chartOfAccount', 'name'),
                        TextInput::make('code')
                            ->label(FilamentUi::field('code')),
                        TextInput::make('name')
                            ->label(FilamentUi::field('name'))
                            ->required(),
                        Textarea::make('description')
                            ->label(FilamentUi::field('description'))
                            ->columnSpanFull(),
                    ]),

                Section::make(FilamentUi::text('Pricing & Quantity'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('unit_of_measure')
                            ->label(FilamentUi::field('unit_of_measure')),
                        TextInput::make('estimated_price')
                            ->label(FilamentUi::field('estimated_price'))
                            ->numeric()
                            ->prefix('$'),
                        TextInput::make('last_purchase_price')
                            ->label(FilamentUi::field('last_purchase_price'))
                            ->numeric()
                            ->prefix('$'),
                        TextInput::make('minimum_order_quantity')
                            ->label(FilamentUi::field('minimum_order_quantity'))
                            ->required()
                            ->numeric()
                            ->default(1),
                        TextInput::make('lead_time_days')
                            ->label(FilamentUi::field('lead_time_days'))
                            ->required()
                            ->numeric()
                            ->default(0),
                    ]),

                Section::make(FilamentUi::text('Specifications'))
                    ->columns(1)
                    ->schema([
                        Textarea::make('specifications')
                            ->label(FilamentUi::field('specifications'))
                            ->columnSpanFull(),
                    ]),

                Section::make(FilamentUi::text('Status'))
                    ->columns(2)
                    ->schema([
                        Toggle::make('is_active')
                            ->label(FilamentUi::field('is_active'))
                            ->required(),
                    ]),
            ]);
    }
}
