<?php

namespace Modules\Procurement\Filament\Resources\VendorBills\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class VendorBillInfolist
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
                        TextEntry::make('vendor.name')
                            ->label(FilamentUi::text('Vendor')),
                        TextEntry::make('purchaseOrder.id')
                            ->label(FilamentUi::text('Purchase order'))
                            ->placeholder('-'),
                        TextEntry::make('goodsReceipt.id')
                            ->label(FilamentUi::text('Goods receipt'))
                            ->placeholder('-'),
                        TextEntry::make('journalEntry.id')
                            ->label(FilamentUi::text('Journal entry'))
                            ->placeholder('-'),
                        TextEntry::make('processed_by')
                            ->label(FilamentUi::field('processed_by'))
                            ->numeric()
                            ->placeholder('-'),
                    ]),

                Section::make(FilamentUi::text('Bill Details'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('bill_number')
                            ->label(FilamentUi::field('bill_number')),
                        TextEntry::make('reference_number')
                            ->label(FilamentUi::field('reference_number'))
                            ->placeholder('-'),
                        TextEntry::make('bill_date')
                            ->label(FilamentUi::field('bill_date'))
                            ->date(),
                        TextEntry::make('due_date')
                            ->label(FilamentUi::field('due_date'))
                            ->date()
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
                        TextEntry::make('tax_amount')
                            ->label(FilamentUi::field('tax_amount'))
                            ->numeric(),
                        TextEntry::make('other_costs')
                            ->label(FilamentUi::field('other_costs'))
                            ->numeric(),
                        TextEntry::make('total_amount')
                            ->label(FilamentUi::field('total_amount'))
                            ->numeric(),
                        TextEntry::make('amount_paid')
                            ->label(FilamentUi::field('amount_paid'))
                            ->numeric(),
                    ]),

                Section::make(FilamentUi::text('Status & Notes'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('status')
                            ->label(FilamentUi::field('status')),
                        TextEntry::make('payment_status')
                            ->label(FilamentUi::field('payment_status')),
                        TextEntry::make('processed_at')
                            ->label(FilamentUi::field('processed_at'))
                            ->dateTime()
                            ->placeholder('-'),
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
