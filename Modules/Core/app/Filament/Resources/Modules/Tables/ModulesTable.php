<?php

namespace Modules\Core\Filament\Resources\Modules\Tables;

use App\Filament\Imports\ModuleImporter;
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

class ModulesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label(FilamentUi::field('code'))
                    ->searchable(),
                TextColumn::make('slug')
                    ->label(FilamentUi::field('slug'))
                    ->searchable(),
                TextColumn::make('name')
                    ->label(FilamentUi::field('name'))
                    ->searchable(),
                TextColumn::make('icon')
                    ->label(FilamentUi::field('icon'))
                    ->searchable(),
                TextColumn::make('color')
                    ->label(FilamentUi::field('color'))
                    ->searchable(),
                TextColumn::make('version')
                    ->label(FilamentUi::field('version'))
                    ->searchable(),
                IconColumn::make('is_core')
                    ->label(FilamentUi::field('is_core'))
                    ->boolean(),
                IconColumn::make('is_active')
                    ->label(FilamentUi::field('is_active'))
                    ->boolean(),
                IconColumn::make('is_premium')
                    ->label(FilamentUi::field('is_premium'))
                    ->boolean(),
                TextColumn::make('price_monthly')
                    ->label(FilamentUi::field('price_monthly'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('price_yearly')
                    ->label(FilamentUi::field('price_yearly'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('sort_order')
                    ->label(FilamentUi::field('sort_order'))
                    ->numeric()
                    ->sortable(),
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
                ...ImportTableActions::make(ModuleImporter::class),
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
