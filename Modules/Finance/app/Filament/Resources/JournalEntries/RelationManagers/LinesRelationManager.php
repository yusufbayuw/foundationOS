<?php

namespace Modules\Finance\Filament\Resources\JournalEntries\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Modules\Core\Support\FilamentUi;

class LinesRelationManager extends RelationManager
{
    protected static string $relationship = 'lines';

    protected static ?string $title = 'Journal Lines';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('chartOfAccount.code')
                    ->label(FilamentUi::text('Account Code')),
                TextColumn::make('chartOfAccount.name')
                    ->label(FilamentUi::text('Account')),
                TextColumn::make('description')
                    ->label(FilamentUi::text('Description')),
                TextColumn::make('debit')
                    ->label(FilamentUi::text('Debit'))
                    ->numeric(decimalPlaces: 2),
                TextColumn::make('credit')
                    ->label(FilamentUi::text('Credit'))
                    ->numeric(decimalPlaces: 2),
            ])
            ->defaultSort('id');
    }

    public function isReadOnly(): bool
    {
        return true;
    }
}
