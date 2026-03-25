<?php

namespace Modules\Procurement\Filament\Resources\ProcurementItems\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ProcurementItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('tenant_id'))
                    ->relationship('tenant', 'name')
                    ->required(),
                Select::make('category_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('category_id'))
                    ->relationship('category', 'name'),
                Select::make('preferred_vendor_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('preferred_vendor_id'))
                    ->relationship('preferredVendor', 'name'),
                Select::make('chart_of_account_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('chart_of_account_id'))
                    ->relationship('chartOfAccount', 'name'),
                TextInput::make('code')
                    ->label(\Modules\Core\Support\FilamentUi::field('code')),
                TextInput::make('name')
                    ->label(\Modules\Core\Support\FilamentUi::field('name'))
                    ->required(),
                Textarea::make('description')
                    ->label(\Modules\Core\Support\FilamentUi::field('description'))
                    ->columnSpanFull(),
                TextInput::make('unit_of_measure')
                    ->label(\Modules\Core\Support\FilamentUi::field('unit_of_measure')),
                TextInput::make('estimated_price')
                    ->label(\Modules\Core\Support\FilamentUi::field('estimated_price'))
                    ->numeric()
                    ->prefix('$'),
                TextInput::make('last_purchase_price')
                    ->label(\Modules\Core\Support\FilamentUi::field('last_purchase_price'))
                    ->numeric()
                    ->prefix('$'),
                Textarea::make('specifications')
                    ->label(\Modules\Core\Support\FilamentUi::field('specifications'))
                    ->columnSpanFull(),
                TextInput::make('minimum_order_quantity')
                    ->label(\Modules\Core\Support\FilamentUi::field('minimum_order_quantity'))
                    ->required()
                    ->numeric()
                    ->default(1),
                TextInput::make('lead_time_days')
                    ->label(\Modules\Core\Support\FilamentUi::field('lead_time_days'))
                    ->required()
                    ->numeric()
                    ->default(0),
                Toggle::make('is_active')
                    ->label(\Modules\Core\Support\FilamentUi::field('is_active'))
                    ->required(),
            ]);
    }
}
