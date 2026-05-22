<?php

namespace Modules\Procurement\Filament\Resources\GoodsReceiptItems\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class GoodsReceiptItemInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('Reference'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('tenant.name')
                            ->label(FilamentUi::text('Tenant')),
                        TextEntry::make('goodsReceipt.id')
                            ->label(FilamentUi::text('Goods receipt')),
                        TextEntry::make('purchaseOrderItem.id')
                            ->label(FilamentUi::text('Purchase order item'))
                            ->placeholder('-'),
                    ]),

                Section::make(FilamentUi::text('Quantities'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('quantity_received')
                            ->label(FilamentUi::field('quantity_received'))
                            ->numeric(),
                        TextEntry::make('quantity_accepted')
                            ->label(FilamentUi::field('quantity_accepted'))
                            ->numeric(),
                        TextEntry::make('quantity_rejected')
                            ->label(FilamentUi::field('quantity_rejected'))
                            ->numeric(),
                        TextEntry::make('condition_status')
                            ->label(FilamentUi::field('condition_status')),
                    ]),

                Section::make(FilamentUi::text('Pricing'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('unit_price')
                            ->label(FilamentUi::field('unit_price'))
                            ->money(),
                        TextEntry::make('total_amount')
                            ->label(FilamentUi::field('total_amount'))
                            ->numeric(),
                    ]),

                Section::make(FilamentUi::text('Notes'))
                    ->columns(1)
                    ->schema([
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
