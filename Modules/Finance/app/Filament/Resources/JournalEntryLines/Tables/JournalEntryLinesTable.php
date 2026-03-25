<?php

namespace Modules\Finance\Filament\Resources\JournalEntryLines\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class JournalEntryLinesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('tenant.name'))
                    ->searchable(),
                TextColumn::make('journalEntry.id')
                    ->label(\Modules\Core\Support\FilamentUi::field('journalEntry.id'))
                    ->searchable(),
                TextColumn::make('chartOfAccount.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('chartOfAccount.name'))
                    ->searchable(),
                TextColumn::make('debit')
                    ->label(\Modules\Core\Support\FilamentUi::field('debit'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('credit')
                    ->label(\Modules\Core\Support\FilamentUi::field('credit'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                                                        ]),
            ]);
    }
}
