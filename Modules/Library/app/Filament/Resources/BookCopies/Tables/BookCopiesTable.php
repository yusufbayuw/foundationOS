<?php

namespace Modules\Library\Filament\Resources\BookCopies\Tables;

use App\Filament\Imports\BookCopyImporter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ImportTableActions;
use Modules\Core\Support\FilamentUi;

class BookCopiesTable
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
                TextColumn::make('book.title')
                    ->label(FilamentUi::field('book.title'))
                    ->searchable(),
                TextColumn::make('copy_number')
                    ->label(FilamentUi::field('copy_number'))
                    ->searchable(),
                TextColumn::make('barcode')
                    ->label(FilamentUi::field('barcode'))
                    ->searchable(),
                TextColumn::make('acquisition_date')
                    ->label(FilamentUi::field('acquisition_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('acquisition_source')
                    ->label(FilamentUi::field('acquisition_source'))
                    ->searchable(),
                TextColumn::make('price')
                    ->label(FilamentUi::field('price'))
                    ->money()
                    ->sortable(),
                TextColumn::make('condition')
                    ->label(FilamentUi::field('condition'))
                    ->searchable(),
                TextColumn::make('status')
                    ->label(FilamentUi::field('status'))
                    ->searchable(),
                TextColumn::make('location_shelf')
                    ->label(FilamentUi::field('location_shelf'))
                    ->searchable(),
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
                ...ImportTableActions::make(BookCopyImporter::class),
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
