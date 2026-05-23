<?php

namespace Modules\Inventory\Filament\Resources\StockItems\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;
use Modules\Inventory\Enums\ValuationMethod;

class StockItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(FilamentUi::text('General information'))
                ->columns(2)
                ->schema([
                    TenantField::make(),
                    Select::make('organization_id')
                        ->label(FilamentUi::field('organization_id'))
                        ->relationship('organization', 'name'),
                    Select::make('procurement_item_id')
                        ->label(FilamentUi::field('procurement_item_id'))
                        ->relationship('procurementItem', 'name'),
                    TextInput::make('code')
                        ->label(FilamentUi::field('code'))
                        ->required(),
                    TextInput::make('name')
                        ->label(FilamentUi::field('name'))
                        ->required(),
                    TextInput::make('sku')
                        ->label(FilamentUi::field('sku')),
                    TextInput::make('unit_of_measure')
                        ->label(FilamentUi::field('unit_of_measure')),
                    Select::make('valuation_method')
                        ->label(FilamentUi::field('valuation_method'))
                        ->options(collect(ValuationMethod::cases())->mapWithKeys(
                            fn (ValuationMethod $m) => [$m->value => $m->label()],
                        )->all())
                        ->default(ValuationMethod::Avg->value)
                        ->required(),
                    Select::make('inventory_coa_id')
                        ->label(FilamentUi::field('inventory_coa_id'))
                        ->relationship('inventoryCoa', 'name'),
                    Select::make('cogs_coa_id')
                        ->label(FilamentUi::field('cogs_coa_id'))
                        ->relationship('cogsCoa', 'name'),
                    Toggle::make('is_active')
                        ->label(FilamentUi::field('is_active'))
                        ->default(true),
                ]),
        ]);
    }
}
