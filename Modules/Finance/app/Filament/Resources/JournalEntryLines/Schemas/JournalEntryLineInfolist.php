<?php

namespace Modules\Finance\Filament\Resources\JournalEntryLines\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class JournalEntryLineInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('Line Details'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('tenant.name')
                            ->label(FilamentUi::text('Tenant')),
                        TextEntry::make('journalEntry.id')
                            ->label(FilamentUi::text('Journal entry')),
                        TextEntry::make('chartOfAccount.name')
                            ->label(FilamentUi::text('Chart of account')),
                        TextEntry::make('description')
                            ->label(FilamentUi::field('description'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ]),

                Section::make(FilamentUi::text('Amounts'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('debit')
                            ->label(FilamentUi::field('debit'))
                            ->numeric(),
                        TextEntry::make('credit')
                            ->label(FilamentUi::field('credit'))
                            ->numeric(),
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
