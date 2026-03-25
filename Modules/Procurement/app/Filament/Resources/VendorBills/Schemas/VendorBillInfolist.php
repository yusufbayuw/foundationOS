<?php

namespace Modules\Procurement\Filament\Resources\VendorBills\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class VendorBillInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tenant.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Tenant')),
                TextEntry::make('vendor.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Vendor')),
                TextEntry::make('purchaseOrder.id')
                    ->label(\Modules\Core\Support\FilamentUi::text('Purchase order'))
                    ->placeholder('-'),
                TextEntry::make('goodsReceipt.id')
                    ->label(\Modules\Core\Support\FilamentUi::text('Goods receipt'))
                    ->placeholder('-'),
                TextEntry::make('journalEntry.id')
                    ->label(\Modules\Core\Support\FilamentUi::text('Journal entry'))
                    ->placeholder('-'),
                TextEntry::make('processed_by')
                    ->label(\Modules\Core\Support\FilamentUi::field('processed_by'))
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('bill_number')
                    ->label(\Modules\Core\Support\FilamentUi::field('bill_number')),
                TextEntry::make('bill_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('bill_date'))
                    ->date(),
                TextEntry::make('due_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('due_date'))
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('reference_number')
                    ->label(\Modules\Core\Support\FilamentUi::field('reference_number'))
                    ->placeholder('-'),
                TextEntry::make('subtotal')
                    ->label(\Modules\Core\Support\FilamentUi::field('subtotal'))
                    ->numeric(),
                TextEntry::make('discount_amount')
                    ->label(\Modules\Core\Support\FilamentUi::field('discount_amount'))
                    ->numeric(),
                TextEntry::make('tax_amount')
                    ->label(\Modules\Core\Support\FilamentUi::field('tax_amount'))
                    ->numeric(),
                TextEntry::make('other_costs')
                    ->label(\Modules\Core\Support\FilamentUi::field('other_costs'))
                    ->numeric(),
                TextEntry::make('total_amount')
                    ->label(\Modules\Core\Support\FilamentUi::field('total_amount'))
                    ->numeric(),
                TextEntry::make('amount_paid')
                    ->label(\Modules\Core\Support\FilamentUi::field('amount_paid'))
                    ->numeric(),
                TextEntry::make('status')
                    ->label(\Modules\Core\Support\FilamentUi::field('status')),
                TextEntry::make('payment_status')
                    ->label(\Modules\Core\Support\FilamentUi::field('payment_status')),
                TextEntry::make('notes')
                    ->label(\Modules\Core\Support\FilamentUi::field('notes'))
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('processed_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('processed_at'))
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
