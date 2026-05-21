<?php

namespace Modules\Finance\Filament\Resources\Payments\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class PaymentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Payment Details')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('tenant.name')
                            ->label(FilamentUi::text('Tenant')),
                        TextEntry::make('studentInvoice.id')
                            ->label(FilamentUi::text('Student invoice')),
                        TextEntry::make('chartOfAccount.name')
                            ->label(FilamentUi::text('Chart of account')),
                        TextEntry::make('payment_number')
                            ->label(FilamentUi::field('payment_number')),
                        TextEntry::make('payment_date')
                            ->label(FilamentUi::field('payment_date'))
                            ->date(),
                        TextEntry::make('amount')
                            ->label(FilamentUi::field('amount'))
                            ->numeric(),
                    ]),

                Section::make('Payment Method')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('payment_method')
                            ->label(FilamentUi::field('payment_method'))
                            ->placeholder('-'),
                        TextEntry::make('payment_channel')
                            ->label(FilamentUi::field('payment_channel'))
                            ->placeholder('-'),
                        TextEntry::make('reference_number')
                            ->label(FilamentUi::field('reference_number'))
                            ->placeholder('-'),
                        TextEntry::make('account_number')
                            ->label(FilamentUi::field('account_number'))
                            ->placeholder('-'),
                        TextEntry::make('account_holder')
                            ->label(FilamentUi::field('account_holder'))
                            ->placeholder('-'),
                        TextEntry::make('bank_name')
                            ->label(FilamentUi::field('bank_name'))
                            ->placeholder('-'),
                        TextEntry::make('proof_file')
                            ->label(FilamentUi::field('proof_file'))
                            ->placeholder('-'),
                    ]),

                Section::make('Verification')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('verified_by')
                            ->label(FilamentUi::field('verified_by'))
                            ->numeric()
                            ->placeholder('-'),
                        TextEntry::make('verified_at')
                            ->label(FilamentUi::field('verified_at'))
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('verification_notes')
                            ->label(FilamentUi::field('verification_notes'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ]),

                Section::make('Reconciliation')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('status')
                            ->label(FilamentUi::field('status')),
                        IconEntry::make('is_reconciled')
                            ->boolean(),
                        TextEntry::make('reconciliation_date')
                            ->label(FilamentUi::field('reconciliation_date'))
                            ->date()
                            ->placeholder('-'),
                    ]),

                Section::make('Timestamps')
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
