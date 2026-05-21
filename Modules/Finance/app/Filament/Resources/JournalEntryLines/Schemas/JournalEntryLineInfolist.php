<?php

namespace Modules\Finance\Filament\Resources\JournalEntryLines\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class JournalEntryLineInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Line Details')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('tenant.name')
                            ->label(\Modules\Core\Support\FilamentUi::text('Tenant')),
                        TextEntry::make('journalEntry.id')
                            ->label(\Modules\Core\Support\FilamentUi::text('Journal entry')),
                        TextEntry::make('chartOfAccount.name')
                            ->label(\Modules\Core\Support\FilamentUi::text('Chart of account')),
                        TextEntry::make('description')
                            ->label(\Modules\Core\Support\FilamentUi::field('description'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ]),

                Section::make('Amounts')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('debit')
                            ->label(\Modules\Core\Support\FilamentUi::field('debit'))
                            ->numeric(),
                        TextEntry::make('credit')
                            ->label(\Modules\Core\Support\FilamentUi::field('credit'))
                            ->numeric(),
                    ]),

                Section::make('Timestamps')
                    ->columns(2)
                    ->schema([
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
