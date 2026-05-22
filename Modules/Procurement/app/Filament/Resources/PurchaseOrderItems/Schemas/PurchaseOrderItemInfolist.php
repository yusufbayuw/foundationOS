<?php

namespace Modules\Procurement\Filament\Resources\PurchaseOrderItems\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class PurchaseOrderItemInfolist
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
                        TextEntry::make('purchaseOrder.id')
                            ->label(FilamentUi::text('Purchase order')),
                        TextEntry::make('purchaseRequisitionItem.id')
                            ->label(FilamentUi::text('Purchase requisition item'))
                            ->placeholder('-'),
                        TextEntry::make('procurementItem.name')
                            ->label(FilamentUi::text('Procurement item'))
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
                        TextEntry::make('quantity')
                            ->label(FilamentUi::field('quantity'))
                            ->numeric(),
                        TextEntry::make('unit_of_measure')
                            ->label(FilamentUi::field('unit_of_measure'))
                            ->placeholder('-'),
                        TextEntry::make('unit_price')
                            ->label(FilamentUi::field('unit_price'))
                            ->money(),
                        TextEntry::make('discount_amount')
                            ->label(FilamentUi::field('discount_amount'))
                            ->numeric(),
                        TextEntry::make('tax_percentage')
                            ->label(FilamentUi::field('tax_percentage'))
                            ->numeric(),
                        TextEntry::make('tax_amount')
                            ->label(FilamentUi::field('tax_amount'))
                            ->numeric(),
                        TextEntry::make('line_total')
                            ->label(FilamentUi::field('line_total'))
                            ->numeric(),
                    ]),

                Section::make(FilamentUi::text('Fulfillment & Status'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('quantity_received')
                            ->label(FilamentUi::field('quantity_received'))
                            ->numeric(),
                        TextEntry::make('status')
                            ->label(FilamentUi::field('status')),
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
