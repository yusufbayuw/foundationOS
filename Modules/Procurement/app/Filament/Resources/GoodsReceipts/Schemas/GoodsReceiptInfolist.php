<?php

namespace Modules\Procurement\Filament\Resources\GoodsReceipts\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class GoodsReceiptInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('Receipt Information'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('tenant.name')
                            ->label(FilamentUi::text('Tenant')),
                        TextEntry::make('purchaseOrder.id')
                            ->label(FilamentUi::text('Purchase order'))
                            ->placeholder('-'),
                        TextEntry::make('receipt_number')
                            ->label(FilamentUi::field('receipt_number')),
                        TextEntry::make('receipt_date')
                            ->label(FilamentUi::field('receipt_date'))
                            ->date(),
                        TextEntry::make('delivery_note_number')
                            ->label(FilamentUi::field('delivery_note_number'))
                            ->placeholder('-'),
                        TextEntry::make('supplier_delivery_number')
                            ->label(FilamentUi::field('supplier_delivery_number'))
                            ->placeholder('-'),
                    ]),

                Section::make(FilamentUi::text('Personnel & Status'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('received_by')
                            ->label(FilamentUi::field('received_by'))
                            ->numeric()
                            ->placeholder('-'),
                        TextEntry::make('inspected_by')
                            ->label(FilamentUi::field('inspected_by'))
                            ->numeric()
                            ->placeholder('-'),
                        TextEntry::make('status')
                            ->label(FilamentUi::field('status')),
                        TextEntry::make('received_at')
                            ->label(FilamentUi::field('received_at'))
                            ->dateTime()
                            ->placeholder('-'),
                    ]),

                Section::make(FilamentUi::text('Notes'))
                    ->columns(1)
                    ->schema([
                        TextEntry::make('notes')
                            ->label(FilamentUi::field('notes'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('inspection_notes')
                            ->label(FilamentUi::field('inspection_notes'))
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
