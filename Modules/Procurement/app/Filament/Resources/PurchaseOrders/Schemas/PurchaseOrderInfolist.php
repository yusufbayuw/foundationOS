<?php

namespace Modules\Procurement\Filament\Resources\PurchaseOrders\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class PurchaseOrderInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('General Information'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('tenant.name')
                            ->label(FilamentUi::text('Tenant')),
                        TextEntry::make('requestForQuotation.id')
                            ->label(FilamentUi::text('Request for quotation'))
                            ->placeholder('-'),
                        TextEntry::make('vendor.name')
                            ->label(FilamentUi::text('Vendor')),
                        TextEntry::make('approved_by')
                            ->label(FilamentUi::field('approved_by'))
                            ->numeric()
                            ->placeholder('-'),
                        TextEntry::make('po_number')
                            ->label(FilamentUi::field('po_number')),
                        TextEntry::make('po_date')
                            ->label(FilamentUi::field('po_date'))
                            ->date(),
                        TextEntry::make('delivery_date')
                            ->label(FilamentUi::field('delivery_date'))
                            ->date()
                            ->placeholder('-'),
                        TextEntry::make('delivery_location')
                            ->label(FilamentUi::field('delivery_location'))
                            ->placeholder('-'),
                        TextEntry::make('payment_terms')
                            ->label(FilamentUi::field('payment_terms'))
                            ->placeholder('-'),
                    ]),

                Section::make(FilamentUi::text('Financial Summary'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('subtotal')
                            ->label(FilamentUi::field('subtotal'))
                            ->numeric(),
                        TextEntry::make('discount_amount')
                            ->label(FilamentUi::field('discount_amount'))
                            ->numeric(),
                        TextEntry::make('tax_percentage')
                            ->label(FilamentUi::field('tax_percentage'))
                            ->numeric(),
                        TextEntry::make('tax_amount')
                            ->label(FilamentUi::field('tax_amount'))
                            ->numeric(),
                        TextEntry::make('shipping_cost')
                            ->label(FilamentUi::field('shipping_cost'))
                            ->money(),
                        TextEntry::make('other_costs')
                            ->label(FilamentUi::field('other_costs'))
                            ->numeric(),
                        TextEntry::make('total_amount')
                            ->label(FilamentUi::field('total_amount'))
                            ->numeric(),
                        TextEntry::make('currency')
                            ->label(FilamentUi::field('currency')),
                        TextEntry::make('exchange_rate')
                            ->label(FilamentUi::field('exchange_rate'))
                            ->numeric(),
                    ]),

                Section::make(FilamentUi::text('Status & Approval'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('status')
                            ->label(FilamentUi::field('status')),
                        TextEntry::make('sent_at')
                            ->label(FilamentUi::field('sent_at'))
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('approved_at')
                            ->label(FilamentUi::field('approved_at'))
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('notes')
                            ->label(FilamentUi::field('notes'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('terms_conditions')
                            ->label(FilamentUi::field('terms_conditions'))
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
