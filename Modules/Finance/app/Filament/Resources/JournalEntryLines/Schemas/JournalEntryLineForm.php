<?php

namespace Modules\Finance\Filament\Resources\JournalEntryLines\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;

class JournalEntryLineForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('Line Details'))
                    ->columns(2)
                    ->schema([
                        TenantField::make(),
                        Select::make('journal_entry_id')
                            ->label(FilamentUi::field('journal_entry_id'))
                            ->relationship('journalEntry', 'id')
                            ->required(),
                        Select::make('chart_of_account_id')
                            ->label(FilamentUi::field('chart_of_account_id'))
                            ->relationship('chartOfAccount', 'name')
                            ->required(),
                        Textarea::make('description')
                            ->label(FilamentUi::field('description'))
                            ->columnSpanFull(),
                    ]),

                Section::make(FilamentUi::text('Amounts'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('debit')
                            ->label(FilamentUi::field('debit'))
                            ->required()
                            ->numeric()
                            ->default(0),
                        TextInput::make('credit')
                            ->label(FilamentUi::field('credit'))
                            ->required()
                            ->numeric()
                            ->default(0),
                    ]),
            ]);
    }
}
