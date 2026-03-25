<?php

namespace Modules\Procurement\Filament\Resources\GoodsReceiptItems\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class GoodsReceiptItemInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tenant.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Tenant')),
                TextEntry::make('goodsReceipt.id')
                    ->label(\Modules\Core\Support\FilamentUi::text('Goods receipt')),
                TextEntry::make('purchaseOrderItem.id')
                    ->label(\Modules\Core\Support\FilamentUi::text('Purchase order item'))
                    ->placeholder('-'),
                TextEntry::make('quantity_received')
                    ->label(\Modules\Core\Support\FilamentUi::field('quantity_received'))
                    ->numeric(),
                TextEntry::make('quantity_accepted')
                    ->label(\Modules\Core\Support\FilamentUi::field('quantity_accepted'))
                    ->numeric(),
                TextEntry::make('quantity_rejected')
                    ->label(\Modules\Core\Support\FilamentUi::field('quantity_rejected'))
                    ->numeric(),
                TextEntry::make('unit_price')
                    ->label(\Modules\Core\Support\FilamentUi::field('unit_price'))
                    ->money(),
                TextEntry::make('total_amount')
                    ->label(\Modules\Core\Support\FilamentUi::field('total_amount'))
                    ->numeric(),
                TextEntry::make('condition_status')
                    ->label(\Modules\Core\Support\FilamentUi::field('condition_status')),
                TextEntry::make('notes')
                    ->label(\Modules\Core\Support\FilamentUi::field('notes'))
                    ->placeholder('-')
                    ->columnSpanFull(),
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
