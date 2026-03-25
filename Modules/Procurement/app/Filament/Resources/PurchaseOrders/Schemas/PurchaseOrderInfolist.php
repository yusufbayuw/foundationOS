<?php

namespace Modules\Procurement\Filament\Resources\PurchaseOrders\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class PurchaseOrderInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tenant.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Tenant')),
                TextEntry::make('requestForQuotation.id')
                    ->label(\Modules\Core\Support\FilamentUi::text('Request for quotation'))
                    ->placeholder('-'),
                TextEntry::make('vendor.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Vendor')),
                TextEntry::make('approved_by')
                    ->label(\Modules\Core\Support\FilamentUi::field('approved_by'))
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('po_number')
                    ->label(\Modules\Core\Support\FilamentUi::field('po_number')),
                TextEntry::make('po_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('po_date'))
                    ->date(),
                TextEntry::make('delivery_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('delivery_date'))
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('delivery_location')
                    ->label(\Modules\Core\Support\FilamentUi::field('delivery_location'))
                    ->placeholder('-'),
                TextEntry::make('payment_terms')
                    ->label(\Modules\Core\Support\FilamentUi::field('payment_terms'))
                    ->placeholder('-'),
                TextEntry::make('subtotal')
                    ->label(\Modules\Core\Support\FilamentUi::field('subtotal'))
                    ->numeric(),
                TextEntry::make('discount_amount')
                    ->label(\Modules\Core\Support\FilamentUi::field('discount_amount'))
                    ->numeric(),
                TextEntry::make('tax_percentage')
                    ->label(\Modules\Core\Support\FilamentUi::field('tax_percentage'))
                    ->numeric(),
                TextEntry::make('tax_amount')
                    ->label(\Modules\Core\Support\FilamentUi::field('tax_amount'))
                    ->numeric(),
                TextEntry::make('shipping_cost')
                    ->label(\Modules\Core\Support\FilamentUi::field('shipping_cost'))
                    ->money(),
                TextEntry::make('other_costs')
                    ->label(\Modules\Core\Support\FilamentUi::field('other_costs'))
                    ->numeric(),
                TextEntry::make('total_amount')
                    ->label(\Modules\Core\Support\FilamentUi::field('total_amount'))
                    ->numeric(),
                TextEntry::make('currency')
                    ->label(\Modules\Core\Support\FilamentUi::field('currency')),
                TextEntry::make('exchange_rate')
                    ->label(\Modules\Core\Support\FilamentUi::field('exchange_rate'))
                    ->numeric(),
                TextEntry::make('status')
                    ->label(\Modules\Core\Support\FilamentUi::field('status')),
                TextEntry::make('sent_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('sent_at'))
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('notes')
                    ->label(\Modules\Core\Support\FilamentUi::field('notes'))
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('terms_conditions')
                    ->label(\Modules\Core\Support\FilamentUi::field('terms_conditions'))
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('approved_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('approved_at'))
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
