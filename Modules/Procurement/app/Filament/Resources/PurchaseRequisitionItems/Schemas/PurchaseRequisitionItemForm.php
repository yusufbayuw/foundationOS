<?php

namespace Modules\Procurement\Filament\Resources\PurchaseRequisitionItems\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;

class PurchaseRequisitionItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('tenant_id'))
                    ->relationship('tenant', 'name')
                    ->required(),
                TextInput::make('purchase_requisition_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('purchase_requisition_id'))
                    ->required()
                    ->numeric(),
                Select::make('procurement_item_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('procurement_item_id'))
                    ->relationship('procurementItem', 'name'),
                Select::make('preferred_vendor_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('preferred_vendor_id'))
                    ->relationship('preferredVendor', 'name'),
                Select::make('department_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('department_id'))
                    ->relationship('department', 'name'),
                Select::make('purchase_order_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('purchase_order_id'))
                    ->relationship('purchaseOrder', 'id'),
                Textarea::make('description')
                    ->label(\Modules\Core\Support\FilamentUi::field('description'))
                    ->columnSpanFull(),
                Textarea::make('specifications')
                    ->label(\Modules\Core\Support\FilamentUi::field('specifications'))
                    ->columnSpanFull(),
                TextInput::make('quantity_requested')
                    ->label(\Modules\Core\Support\FilamentUi::field('quantity_requested'))
                    ->required()
                    ->numeric()
                    ->default(1),
                TextInput::make('unit_of_measure')
                    ->label(\Modules\Core\Support\FilamentUi::field('unit_of_measure')),
                TextInput::make('estimated_unit_price')
                    ->label(\Modules\Core\Support\FilamentUi::field('estimated_unit_price'))
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->prefix('$'),
                TextInput::make('estimated_total_price')
                    ->label(\Modules\Core\Support\FilamentUi::field('estimated_total_price'))
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->prefix('$'),
                DatePicker::make('required_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('required_date')),
                Textarea::make('usage_purpose')
                    ->label(\Modules\Core\Support\FilamentUi::field('usage_purpose'))
                    ->columnSpanFull(),
                Select::make('budget_account_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('budget_account_id'))
                    ->relationship('budgetAccount', 'name'),
                TextInput::make('status')
                    ->label(\Modules\Core\Support\FilamentUi::field('status'))
                    ->required()
                    ->default('requested'),
                TextInput::make('ordered_quantity')
                    ->label(\Modules\Core\Support\FilamentUi::field('ordered_quantity'))
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('received_quantity')
                    ->label(\Modules\Core\Support\FilamentUi::field('received_quantity'))
                    ->required()
                    ->numeric()
                    ->default(0),
                Textarea::make('rejection_reason')
                    ->label(\Modules\Core\Support\FilamentUi::field('rejection_reason'))
                    ->columnSpanFull(),
                Textarea::make('notes')
                    ->label(\Modules\Core\Support\FilamentUi::field('notes'))
                    ->columnSpanFull(),
            ]);
    }
}
