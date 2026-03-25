<?php

namespace Modules\Procurement\Filament\Resources\GoodsReceipts\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class GoodsReceiptInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tenant.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Tenant')),
                TextEntry::make('purchaseOrder.id')
                    ->label(\Modules\Core\Support\FilamentUi::text('Purchase order'))
                    ->placeholder('-'),
                TextEntry::make('received_by')
                    ->label(\Modules\Core\Support\FilamentUi::field('received_by'))
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('inspected_by')
                    ->label(\Modules\Core\Support\FilamentUi::field('inspected_by'))
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('receipt_number')
                    ->label(\Modules\Core\Support\FilamentUi::field('receipt_number')),
                TextEntry::make('receipt_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('receipt_date'))
                    ->date(),
                TextEntry::make('delivery_note_number')
                    ->label(\Modules\Core\Support\FilamentUi::field('delivery_note_number'))
                    ->placeholder('-'),
                TextEntry::make('supplier_delivery_number')
                    ->label(\Modules\Core\Support\FilamentUi::field('supplier_delivery_number'))
                    ->placeholder('-'),
                TextEntry::make('status')
                    ->label(\Modules\Core\Support\FilamentUi::field('status')),
                TextEntry::make('notes')
                    ->label(\Modules\Core\Support\FilamentUi::field('notes'))
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('inspection_notes')
                    ->label(\Modules\Core\Support\FilamentUi::field('inspection_notes'))
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('received_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('received_at'))
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('created_at'))
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('updated_at'))
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
