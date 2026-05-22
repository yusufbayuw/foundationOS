<?php

namespace Modules\Finance\Filament\Resources\StudentInvoices\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class StudentInvoiceInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('Invoice Details'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('tenant.name')
                            ->label(FilamentUi::text('Tenant')),
                        TextEntry::make('tuitionType.name')
                            ->label(FilamentUi::text('Tuition type'))
                            ->placeholder('-'),
                        TextEntry::make('invoice_number')
                            ->label(FilamentUi::field('invoice_number')),
                        TextEntry::make('invoice_type')
                            ->label(FilamentUi::field('invoice_type'))
                            ->placeholder('-'),
                        TextEntry::make('issue_date')
                            ->label(FilamentUi::field('issue_date'))
                            ->date(),
                        TextEntry::make('due_date')
                            ->label(FilamentUi::field('due_date'))
                            ->date(),
                        TextEntry::make('status')
                            ->label(FilamentUi::field('status')),
                    ]),

                Section::make(FilamentUi::text('Amount'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('amount')
                            ->label(FilamentUi::field('amount'))
                            ->numeric(),
                        TextEntry::make('discount_amount')
                            ->label(FilamentUi::field('discount_amount'))
                            ->numeric(),
                        TextEntry::make('discount_reason')
                            ->label(FilamentUi::field('discount_reason'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('penalty_amount')
                            ->label(FilamentUi::field('penalty_amount'))
                            ->numeric(),
                        TextEntry::make('total_amount')
                            ->label(FilamentUi::field('total_amount'))
                            ->numeric(),
                        TextEntry::make('paid_amount')
                            ->label(FilamentUi::field('paid_amount'))
                            ->numeric(),
                        TextEntry::make('remaining_amount')
                            ->label(FilamentUi::field('remaining_amount'))
                            ->numeric(),
                    ]),

                Section::make(FilamentUi::text('Notes'))
                    ->columns(1)
                    ->schema([
                        TextEntry::make('description')
                            ->label(FilamentUi::field('description'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('notes')
                            ->label(FilamentUi::field('notes'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ]),

                Section::make(FilamentUi::text('Delivery'))
                    ->columns(2)
                    ->schema([
                        IconEntry::make('is_sent')
                            ->boolean(),
                        TextEntry::make('sent_at')
                            ->label(FilamentUi::field('sent_at'))
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('sent_via')
                            ->label(FilamentUi::field('sent_via'))
                            ->placeholder('-'),
                        TextEntry::make('invoiceable_type')
                            ->label(FilamentUi::field('invoiceable_type')),
                        TextEntry::make('invoiceable_id')
                            ->label(FilamentUi::field('invoiceable_id'))
                            ->numeric(),
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
