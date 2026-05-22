<?php

namespace Modules\Finance\Filament\Resources\JournalEntries\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class JournalEntryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('Entry Details'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('tenant.name')
                            ->label(FilamentUi::text('Tenant')),
                        TextEntry::make('organization.name')
                            ->label(FilamentUi::text('Organization')),
                        TextEntry::make('reversedEntry.id')
                            ->label(FilamentUi::text('Reversed entry'))
                            ->placeholder('-'),
                        TextEntry::make('entry_number')
                            ->label(FilamentUi::field('entry_number')),
                        TextEntry::make('date')
                            ->label(FilamentUi::field('date'))
                            ->date(),
                        TextEntry::make('description')
                            ->label(FilamentUi::field('description'))
                            ->columnSpanFull(),
                    ]),

                Section::make(FilamentUi::text('Totals'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('total_debit')
                            ->label(FilamentUi::field('total_debit'))
                            ->numeric(),
                        TextEntry::make('total_credit')
                            ->label(FilamentUi::field('total_credit'))
                            ->numeric(),
                        IconEntry::make('is_balanced')
                            ->boolean(),
                    ]),

                Section::make(FilamentUi::text('Posting'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('posted_by')
                            ->label(FilamentUi::field('posted_by'))
                            ->numeric()
                            ->placeholder('-'),
                        TextEntry::make('posted_at')
                            ->label(FilamentUi::field('posted_at'))
                            ->dateTime()
                            ->placeholder('-'),
                        IconEntry::make('is_posted')
                            ->boolean(),
                    ]),

                Section::make(FilamentUi::text('Reversal'))
                    ->columns(2)
                    ->schema([
                        IconEntry::make('is_reversed')
                            ->boolean(),
                        TextEntry::make('reversal_reason')
                            ->label(FilamentUi::field('reversal_reason'))
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
