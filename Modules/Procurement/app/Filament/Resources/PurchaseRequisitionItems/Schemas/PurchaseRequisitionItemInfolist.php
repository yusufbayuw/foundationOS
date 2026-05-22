<?php

namespace Modules\Procurement\Filament\Resources\PurchaseRequisitionItems\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class PurchaseRequisitionItemInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('References'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('tenant.name')
                            ->label(FilamentUi::text('Tenant')),
                        TextEntry::make('purchase_requisition_id')
                            ->label(FilamentUi::field('purchase_requisition_id'))
                            ->numeric(),
                        TextEntry::make('procurementItem.name')
                            ->label(FilamentUi::text('Procurement item'))
                            ->placeholder('-'),
                        TextEntry::make('preferredVendor.name')
                            ->label(FilamentUi::text('Preferred vendor'))
                            ->placeholder('-'),
                        TextEntry::make('department.name')
                            ->label(FilamentUi::text('Department'))
                            ->placeholder('-'),
                        TextEntry::make('purchaseOrder.id')
                            ->label(FilamentUi::text('Purchase order'))
                            ->placeholder('-'),
                        TextEntry::make('description')
                            ->label(FilamentUi::field('description'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('specifications')
                            ->label(FilamentUi::field('specifications'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ]),

                Section::make(FilamentUi::text('Quantity & Pricing'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('quantity_requested')
                            ->label(FilamentUi::field('quantity_requested'))
                            ->numeric(),
                        TextEntry::make('unit_of_measure')
                            ->label(FilamentUi::field('unit_of_measure'))
                            ->placeholder('-'),
                        TextEntry::make('estimated_unit_price')
                            ->label(FilamentUi::field('estimated_unit_price'))
                            ->money(),
                        TextEntry::make('estimated_total_price')
                            ->label(FilamentUi::field('estimated_total_price'))
                            ->money(),
                        TextEntry::make('required_date')
                            ->label(FilamentUi::field('required_date'))
                            ->date()
                            ->placeholder('-'),
                        TextEntry::make('budgetAccount.name')
                            ->label(FilamentUi::text('Budget account'))
                            ->placeholder('-'),
                        TextEntry::make('usage_purpose')
                            ->label(FilamentUi::field('usage_purpose'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ]),

                Section::make(FilamentUi::text('Fulfillment & Status'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('status')
                            ->label(FilamentUi::field('status')),
                        TextEntry::make('ordered_quantity')
                            ->label(FilamentUi::field('ordered_quantity'))
                            ->numeric(),
                        TextEntry::make('received_quantity')
                            ->label(FilamentUi::field('received_quantity'))
                            ->numeric(),
                        TextEntry::make('rejection_reason')
                            ->label(FilamentUi::field('rejection_reason'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('notes')
                            ->label(FilamentUi::field('notes'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ]),

                Section::make(FilamentUi::text('Timestamps'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('created_at')
                            ->label(FilamentUi::field('created_at'))
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('updated_at')
                            ->label(FilamentUi::field('updated_at'))
                            ->dateTime()
                            ->placeholder('-'),
                    ]),
            ]);
    }
}
