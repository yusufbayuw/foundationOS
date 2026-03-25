<?php

namespace Modules\Finance\Filament\Resources\JournalEntries\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class JournalEntriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('tenant.name'))
                    ->searchable(),
                TextColumn::make('organization.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('organization.name'))
                    ->searchable(),
                TextColumn::make('posted_by')
                    ->label(\Modules\Core\Support\FilamentUi::field('posted_by'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('reversedEntry.id')
                    ->label(\Modules\Core\Support\FilamentUi::field('reversedEntry.id'))
                    ->searchable(),
                TextColumn::make('entry_number')
                    ->label(\Modules\Core\Support\FilamentUi::field('entry_number'))
                    ->searchable(),
                TextColumn::make('date')
                    ->label(\Modules\Core\Support\FilamentUi::field('date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('total_debit')
                    ->label(\Modules\Core\Support\FilamentUi::field('total_debit'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total_credit')
                    ->label(\Modules\Core\Support\FilamentUi::field('total_credit'))
                    ->numeric()
                    ->sortable(),
                IconColumn::make('is_balanced')
                    ->label(\Modules\Core\Support\FilamentUi::field('is_balanced'))
                    ->boolean(),
                IconColumn::make('is_posted')
                    ->label(\Modules\Core\Support\FilamentUi::field('is_posted'))
                    ->boolean(),
                TextColumn::make('posted_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('posted_at'))
                    ->dateTime()
                    ->sortable(),
                IconColumn::make('is_reversed')
                    ->label(\Modules\Core\Support\FilamentUi::field('is_reversed'))
                    ->boolean(),
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
