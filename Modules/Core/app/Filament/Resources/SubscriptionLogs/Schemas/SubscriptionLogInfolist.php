<?php

namespace Modules\Core\Filament\Resources\SubscriptionLogs\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class SubscriptionLogInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic Info')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('tenant.name')
                            ->label(FilamentUi::text('Tenant')),
                        TextEntry::make('action')
                            ->label(FilamentUi::field('action')),
                        TextEntry::make('previousPlan.name')
                            ->label(FilamentUi::text('Previous plan'))
                            ->placeholder('-'),
                        TextEntry::make('newPlan.name')
                            ->label(FilamentUi::text('New plan'))
                            ->placeholder('-'),
                    ]),

                Section::make('Payment')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('amount')
                            ->label(FilamentUi::field('amount'))
                            ->numeric()
                            ->placeholder('-'),
                        TextEntry::make('currency')
                            ->label(FilamentUi::field('currency'))
                            ->placeholder('-'),
                        TextEntry::make('payment_method')
                            ->label(FilamentUi::field('payment_method'))
                            ->placeholder('-'),
                        TextEntry::make('payment_status')
                            ->label(FilamentUi::field('payment_status'))
                            ->placeholder('-'),
                        TextEntry::make('payment_proof')
                            ->label(FilamentUi::field('payment_proof'))
                            ->placeholder('-'),
                        TextEntry::make('invoice_number')
                            ->label(FilamentUi::field('invoice_number'))
                            ->placeholder('-'),
                        TextEntry::make('invoice_url')
                            ->label(FilamentUi::field('invoice_url'))
                            ->placeholder('-'),
                    ]),

                Section::make('Period')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('period_start')
                            ->label(FilamentUi::field('period_start'))
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('period_end')
                            ->label(FilamentUi::field('period_end'))
                            ->dateTime()
                            ->placeholder('-'),
                    ]),

                Section::make('Additional')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('notes')
                            ->label(FilamentUi::field('notes'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('processed_by')
                            ->label(FilamentUi::field('processed_by'))
                            ->numeric()
                            ->placeholder('-'),
                        TextEntry::make('metadata')
                            ->label(FilamentUi::field('metadata'))
                            ->placeholder('-')
                            ->columnSpanFull(),
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
