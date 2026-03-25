<?php

namespace Modules\Finance\Filament\Resources\Payments\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class PaymentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tenant.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Tenant')),
                TextEntry::make('studentInvoice.id')
                    ->label(\Modules\Core\Support\FilamentUi::text('Student invoice')),
                TextEntry::make('chartOfAccount.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Chart of account')),
                TextEntry::make('verified_by')
                    ->label(\Modules\Core\Support\FilamentUi::field('verified_by'))
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('payment_number')
                    ->label(\Modules\Core\Support\FilamentUi::field('payment_number')),
                TextEntry::make('payment_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('payment_date'))
                    ->date(),
                TextEntry::make('amount')
                    ->label(\Modules\Core\Support\FilamentUi::field('amount'))
                    ->numeric(),
                TextEntry::make('payment_method')
                    ->label(\Modules\Core\Support\FilamentUi::field('payment_method'))
                    ->placeholder('-'),
                TextEntry::make('payment_channel')
                    ->label(\Modules\Core\Support\FilamentUi::field('payment_channel'))
                    ->placeholder('-'),
                TextEntry::make('reference_number')
                    ->label(\Modules\Core\Support\FilamentUi::field('reference_number'))
                    ->placeholder('-'),
                TextEntry::make('account_number')
                    ->label(\Modules\Core\Support\FilamentUi::field('account_number'))
                    ->placeholder('-'),
                TextEntry::make('account_holder')
                    ->label(\Modules\Core\Support\FilamentUi::field('account_holder'))
                    ->placeholder('-'),
                TextEntry::make('bank_name')
                    ->label(\Modules\Core\Support\FilamentUi::field('bank_name'))
                    ->placeholder('-'),
                TextEntry::make('proof_file')
                    ->label(\Modules\Core\Support\FilamentUi::field('proof_file'))
                    ->placeholder('-'),
                TextEntry::make('verified_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('verified_at'))
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('verification_notes')
                    ->label(\Modules\Core\Support\FilamentUi::field('verification_notes'))
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('status')
                    ->label(\Modules\Core\Support\FilamentUi::field('status')),
                IconEntry::make('is_reconciled')
                    ->boolean(),
                TextEntry::make('reconciliation_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('reconciliation_date'))
                    ->date()
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
