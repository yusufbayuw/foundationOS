<?php

namespace Modules\Library\Filament\Resources\BookCopies\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BookCopiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('tenant.name'))
                    ->searchable(),
                TextColumn::make('book.title')
                    ->label(\Modules\Core\Support\FilamentUi::field('book.title'))
                    ->searchable(),
                TextColumn::make('copy_number')
                    ->label(\Modules\Core\Support\FilamentUi::field('copy_number'))
                    ->searchable(),
                TextColumn::make('barcode')
                    ->label(\Modules\Core\Support\FilamentUi::field('barcode'))
                    ->searchable(),
                TextColumn::make('acquisition_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('acquisition_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('acquisition_source')
                    ->label(\Modules\Core\Support\FilamentUi::field('acquisition_source'))
                    ->searchable(),
                TextColumn::make('price')
                    ->label(\Modules\Core\Support\FilamentUi::field('price'))
                    ->money()
                    ->sortable(),
                TextColumn::make('condition')
                    ->label(\Modules\Core\Support\FilamentUi::field('condition'))
                    ->searchable(),
                TextColumn::make('status')
                    ->label(\Modules\Core\Support\FilamentUi::field('status'))
                    ->searchable(),
                TextColumn::make('location_shelf')
                    ->label(\Modules\Core\Support\FilamentUi::field('location_shelf'))
                    ->searchable(),
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
