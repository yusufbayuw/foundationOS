<?php

namespace Modules\Procurement\Filament\Resources\VendorBillItems\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class VendorBillItemInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tenant.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Tenant')),
                TextEntry::make('vendorBill.id')
                    ->label(\Modules\Core\Support\FilamentUi::text('Vendor bill')),
                TextEntry::make('purchaseOrderItem.id')
                    ->label(\Modules\Core\Support\FilamentUi::text('Purchase order item'))
                    ->placeholder('-'),
                TextEntry::make('goodsReceiptItem.id')
                    ->label(\Modules\Core\Support\FilamentUi::text('Goods receipt item'))
                    ->placeholder('-'),
                TextEntry::make('description')
                    ->label(\Modules\Core\Support\FilamentUi::field('description'))
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('quantity')
                    ->label(\Modules\Core\Support\FilamentUi::field('quantity'))
                    ->numeric(),
                TextEntry::make('unit_of_measure')
                    ->label(\Modules\Core\Support\FilamentUi::field('unit_of_measure'))
                    ->placeholder('-'),
                TextEntry::make('unit_price')
                    ->label(\Modules\Core\Support\FilamentUi::field('unit_price'))
                    ->money(),
                TextEntry::make('tax_amount')
                    ->label(\Modules\Core\Support\FilamentUi::field('tax_amount'))
                    ->numeric(),
                TextEntry::make('line_total')
                    ->label(\Modules\Core\Support\FilamentUi::field('line_total'))
                    ->numeric(),
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
