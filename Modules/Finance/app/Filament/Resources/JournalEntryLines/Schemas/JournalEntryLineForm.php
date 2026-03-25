<?php

namespace Modules\Finance\Filament\Resources\JournalEntryLines\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class JournalEntryLineForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('tenant_id'))
                    ->relationship('tenant', 'name')
                    ->required(),
                Select::make('journal_entry_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('journal_entry_id'))
                    ->relationship('journalEntry', 'id')
                    ->required(),
                Select::make('chart_of_account_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('chart_of_account_id'))
                    ->relationship('chartOfAccount', 'name')
                    ->required(),
                Textarea::make('description')
                    ->label(\Modules\Core\Support\FilamentUi::field('description'))
                    ->columnSpanFull(),
                TextInput::make('debit')
                    ->label(\Modules\Core\Support\FilamentUi::field('debit'))
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('credit')
                    ->label(\Modules\Core\Support\FilamentUi::field('credit'))
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
