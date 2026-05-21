<?php

namespace Modules\Finance\Filament\Resources\JournalEntries\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class JournalEntryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Entry Details')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('tenant.name')
                            ->label(\Modules\Core\Support\FilamentUi::text('Tenant')),
                        TextEntry::make('organization.name')
                            ->label(\Modules\Core\Support\FilamentUi::text('Organization')),
                        TextEntry::make('reversedEntry.id')
                            ->label(\Modules\Core\Support\FilamentUi::text('Reversed entry'))
                            ->placeholder('-'),
                        TextEntry::make('entry_number')
                            ->label(\Modules\Core\Support\FilamentUi::field('entry_number')),
                        TextEntry::make('date')
                            ->label(\Modules\Core\Support\FilamentUi::field('date'))
                            ->date(),
                        TextEntry::make('description')
                            ->label(\Modules\Core\Support\FilamentUi::field('description'))
                            ->columnSpanFull(),
                    ]),

                Section::make('Totals')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('total_debit')
                            ->label(\Modules\Core\Support\FilamentUi::field('total_debit'))
                            ->numeric(),
                        TextEntry::make('total_credit')
                            ->label(\Modules\Core\Support\FilamentUi::field('total_credit'))
                            ->numeric(),
                        IconEntry::make('is_balanced')
                            ->boolean(),
                    ]),

                Section::make('Posting')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('posted_by')
                            ->label(\Modules\Core\Support\FilamentUi::field('posted_by'))
                            ->numeric()
                            ->placeholder('-'),
                        TextEntry::make('posted_at')
                            ->label(\Modules\Core\Support\FilamentUi::field('posted_at'))
                            ->dateTime()
                            ->placeholder('-'),
                        IconEntry::make('is_posted')
                            ->boolean(),
                    ]),

                Section::make('Reversal')
                    ->columns(2)
                    ->schema([
                        IconEntry::make('is_reversed')
                            ->boolean(),
                        TextEntry::make('reversal_reason')
                            ->label(\Modules\Core\Support\FilamentUi::field('reversal_reason'))
                            ->placeholder('-')
                            ->columnSpanFull(),
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
