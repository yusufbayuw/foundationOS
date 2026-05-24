<?php

namespace Modules\Finance\Filament\Resources\CustomerInvoiceItems\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Modules\Finance\Models\CustomerInvoiceItem;

class CustomerInvoiceItemInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tenant.name')
                    ->label('Tenant'),
                TextEntry::make('customerInvoice.id')
                    ->label('Customer invoice'),
                TextEntry::make('description'),
                TextEntry::make('quantity')
                    ->numeric(),
                TextEntry::make('unit')
                    ->placeholder('-'),
                TextEntry::make('unit_price')
                    ->money(),
                TextEntry::make('discount_amount')
                    ->numeric(),
                TextEntry::make('line_total')
                    ->numeric(),
                TextEntry::make('chartOfAccount.name')
                    ->label('Chart of account')
                    ->placeholder('-'),
                TextEntry::make('sort_order')
                    ->numeric(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn (CustomerInvoiceItem $record): bool => $record->trashed()),
            ]);
    }
}
