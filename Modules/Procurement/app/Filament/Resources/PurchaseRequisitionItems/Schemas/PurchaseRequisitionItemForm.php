<?php

namespace Modules\Procurement\Filament\Resources\PurchaseRequisitionItems\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;

class PurchaseRequisitionItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('References'))
                    ->columns(2)
                    ->schema([
                        TenantField::make(),
                        TextInput::make('purchase_requisition_id')
                            ->label(FilamentUi::field('purchase_requisition_id'))
                            ->required()
                            ->numeric(),
                        Select::make('procurement_item_id')
                            ->label(FilamentUi::field('procurement_item_id'))
                            ->relationship('procurementItem', 'name'),
                        Select::make('preferred_vendor_id')
                            ->label(FilamentUi::field('preferred_vendor_id'))
                            ->relationship('preferredVendor', 'name'),
                        Select::make('department_id')
                            ->label(FilamentUi::field('department_id'))
                            ->relationship('department', 'name'),
                        Select::make('purchase_order_id')
                            ->label(FilamentUi::field('purchase_order_id'))
                            ->relationship('purchaseOrder', 'id'),
                        Textarea::make('description')
                            ->label(FilamentUi::field('description'))
                            ->columnSpanFull(),
                        Textarea::make('specifications')
                            ->label(FilamentUi::field('specifications'))
                            ->columnSpanFull(),
                    ]),

                Section::make(FilamentUi::text('Quantity & Pricing'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('quantity_requested')
                            ->label(FilamentUi::field('quantity_requested'))
                            ->required()
                            ->numeric()
                            ->default(1),
                        TextInput::make('unit_of_measure')
                            ->label(FilamentUi::field('unit_of_measure')),
                        TextInput::make('estimated_unit_price')
                            ->label(FilamentUi::field('estimated_unit_price'))
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->prefix('$'),
                        TextInput::make('estimated_total_price')
                            ->label(FilamentUi::field('estimated_total_price'))
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->prefix('$'),
                        DatePicker::make('required_date')
                            ->label(FilamentUi::field('required_date')),
                        Select::make('budget_account_id')
                            ->label(FilamentUi::field('budget_account_id'))
                            ->relationship('budgetAccount', 'name'),
                        Textarea::make('usage_purpose')
                            ->label(FilamentUi::field('usage_purpose'))
                            ->columnSpanFull(),
                    ]),

                Section::make(FilamentUi::text('Fulfillment & Status'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('status')
                            ->label(FilamentUi::field('status'))
                            ->required()
                            ->default('requested'),
                        TextInput::make('ordered_quantity')
                            ->label(FilamentUi::field('ordered_quantity'))
                            ->required()
                            ->numeric()
                            ->default(0),
                        TextInput::make('received_quantity')
                            ->label(FilamentUi::field('received_quantity'))
                            ->required()
                            ->numeric()
                            ->default(0),
                        Textarea::make('rejection_reason')
                            ->label(FilamentUi::field('rejection_reason'))
                            ->columnSpanFull(),
                        Textarea::make('notes')
                            ->label(FilamentUi::field('notes'))
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
