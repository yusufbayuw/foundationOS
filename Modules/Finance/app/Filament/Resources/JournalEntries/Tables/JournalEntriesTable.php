<?php

namespace Modules\Finance\Filament\Resources\JournalEntries\Tables;

use App\Filament\Imports\JournalEntryImporter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ImportTableActions;
use Modules\Core\Support\FilamentUi;

class JournalEntriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->label(FilamentUi::field('tenant.name'))
                    ->searchable(),
                TextColumn::make('organization.name')
                    ->label(FilamentUi::field('organization.name'))
                    ->searchable(),
                TextColumn::make('posted_by')
                    ->label(FilamentUi::field('posted_by'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('reversedEntry.id')
                    ->label(FilamentUi::field('reversedEntry.id'))
                    ->searchable(),
                TextColumn::make('entry_number')
                    ->label(FilamentUi::field('entry_number'))
                    ->searchable(),
                TextColumn::make('date')
                    ->label(FilamentUi::field('date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('total_debit')
                    ->label(FilamentUi::field('total_debit'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total_credit')
                    ->label(FilamentUi::field('total_credit'))
                    ->numeric()
                    ->sortable(),
                IconColumn::make('is_balanced')
                    ->label(FilamentUi::field('is_balanced'))
                    ->boolean(),
                IconColumn::make('is_posted')
                    ->label(FilamentUi::field('is_posted'))
                    ->boolean(),
                TextColumn::make('posted_at')
                    ->label(FilamentUi::field('posted_at'))
                    ->dateTime()
                    ->sortable(),
                IconColumn::make('is_reversed')
                    ->label(FilamentUi::field('is_reversed'))
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label(FilamentUi::field('created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(FilamentUi::field('updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->headerActions([
                ...ImportTableActions::make(JournalEntryImporter::class),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ]);
    }
}
