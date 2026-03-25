<?php

namespace Modules\Core\Filament\Resources\Modules\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ModulesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label(\Modules\Core\Support\FilamentUi::field('code'))
                    ->searchable(),
                TextColumn::make('slug')
                    ->label(\Modules\Core\Support\FilamentUi::field('slug'))
                    ->searchable(),
                TextColumn::make('name')
                    ->label(\Modules\Core\Support\FilamentUi::field('name'))
                    ->searchable(),
                TextColumn::make('icon')
                    ->label(\Modules\Core\Support\FilamentUi::field('icon'))
                    ->searchable(),
                TextColumn::make('color')
                    ->label(\Modules\Core\Support\FilamentUi::field('color'))
                    ->searchable(),
                TextColumn::make('version')
                    ->label(\Modules\Core\Support\FilamentUi::field('version'))
                    ->searchable(),
                IconColumn::make('is_core')
                    ->label(\Modules\Core\Support\FilamentUi::field('is_core'))
                    ->boolean(),
                IconColumn::make('is_active')
                    ->label(\Modules\Core\Support\FilamentUi::field('is_active'))
                    ->boolean(),
                IconColumn::make('is_premium')
                    ->label(\Modules\Core\Support\FilamentUi::field('is_premium'))
                    ->boolean(),
                TextColumn::make('price_monthly')
                    ->label(\Modules\Core\Support\FilamentUi::field('price_monthly'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('price_yearly')
                    ->label(\Modules\Core\Support\FilamentUi::field('price_yearly'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('sort_order')
                    ->label(\Modules\Core\Support\FilamentUi::field('sort_order'))
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
