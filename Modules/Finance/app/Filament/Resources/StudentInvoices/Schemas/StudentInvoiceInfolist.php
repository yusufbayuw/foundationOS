<?php

namespace Modules\Finance\Filament\Resources\StudentInvoices\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class StudentInvoiceInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Invoice Details')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('tenant.name')
                            ->label(\Modules\Core\Support\FilamentUi::text('Tenant')),
                        TextEntry::make('tuitionType.name')
                            ->label(\Modules\Core\Support\FilamentUi::text('Tuition type'))
                            ->placeholder('-'),
                        TextEntry::make('invoice_number')
                            ->label(\Modules\Core\Support\FilamentUi::field('invoice_number')),
                        TextEntry::make('invoice_type')
                            ->label(\Modules\Core\Support\FilamentUi::field('invoice_type'))
                            ->placeholder('-'),
                        TextEntry::make('issue_date')
                            ->label(\Modules\Core\Support\FilamentUi::field('issue_date'))
                            ->date(),
                        TextEntry::make('due_date')
                            ->label(\Modules\Core\Support\FilamentUi::field('due_date'))
                            ->date(),
                        TextEntry::make('status')
                            ->label(\Modules\Core\Support\FilamentUi::field('status')),
                    ]),

                Section::make('Amount')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('amount')
                            ->label(\Modules\Core\Support\FilamentUi::field('amount'))
                            ->numeric(),
                        TextEntry::make('discount_amount')
                            ->label(\Modules\Core\Support\FilamentUi::field('discount_amount'))
                            ->numeric(),
                        TextEntry::make('discount_reason')
                            ->label(\Modules\Core\Support\FilamentUi::field('discount_reason'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('penalty_amount')
                            ->label(\Modules\Core\Support\FilamentUi::field('penalty_amount'))
                            ->numeric(),
                        TextEntry::make('total_amount')
                            ->label(\Modules\Core\Support\FilamentUi::field('total_amount'))
                            ->numeric(),
                        TextEntry::make('paid_amount')
                            ->label(\Modules\Core\Support\FilamentUi::field('paid_amount'))
                            ->numeric(),
                        TextEntry::make('remaining_amount')
                            ->label(\Modules\Core\Support\FilamentUi::field('remaining_amount'))
                            ->numeric(),
                    ]),

                Section::make('Notes')
                    ->columns(1)
                    ->schema([
                        TextEntry::make('description')
                            ->label(\Modules\Core\Support\FilamentUi::field('description'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('notes')
                            ->label(\Modules\Core\Support\FilamentUi::field('notes'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ]),

                Section::make('Delivery')
                    ->columns(2)
                    ->schema([
                        IconEntry::make('is_sent')
                            ->boolean(),
                        TextEntry::make('sent_at')
                            ->label(\Modules\Core\Support\FilamentUi::field('sent_at'))
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('sent_via')
                            ->label(\Modules\Core\Support\FilamentUi::field('sent_via'))
                            ->placeholder('-'),
                        TextEntry::make('invoiceable_type')
                            ->label(\Modules\Core\Support\FilamentUi::field('invoiceable_type')),
                        TextEntry::make('invoiceable_id')
                            ->label(\Modules\Core\Support\FilamentUi::field('invoiceable_id'))
                            ->numeric(),
                        TextEntry::make('created_at')
                            ->label(\Modules\Core\Support\FilamentUi::field('created_at'))
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('updated_at')
                            ->label(\Modules\Core\Support\FilamentUi::field('updated_at'))
                            ->dateTime()
                            ->placeholder('-'),
                    ]),
            ]);
    }
}
