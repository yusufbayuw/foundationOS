<?php

namespace Modules\Procurement\Filament\Resources\VendorBillItems\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class VendorBillItemInfolist
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
                        TextEntry::make('vendorBill.id')
                            ->label(FilamentUi::text('Vendor bill')),
                        TextEntry::make('purchaseOrderItem.id')
                            ->label(FilamentUi::text('Purchase order item'))
                            ->placeholder('-'),
                        TextEntry::make('goodsReceiptItem.id')
                            ->label(FilamentUi::text('Goods receipt item'))
                            ->placeholder('-'),
                        TextEntry::make('description')
                            ->label(FilamentUi::field('description'))
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
                        TextEntry::make('tax_amount')
                            ->label(FilamentUi::field('tax_amount'))
                            ->numeric(),
                        TextEntry::make('line_total')
                            ->label(FilamentUi::field('line_total'))
                            ->numeric(),
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
