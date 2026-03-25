<?php

namespace Modules\Employee\Filament\Resources\KpiIndicators\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class KpiIndicatorsTable
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
                TextColumn::make('code')
                    ->label(\Modules\Core\Support\FilamentUi::field('code'))
                    ->searchable(),
                TextColumn::make('name')
                    ->label(\Modules\Core\Support\FilamentUi::field('name'))
                    ->searchable(),
                TextColumn::make('category')
                    ->label(\Modules\Core\Support\FilamentUi::field('category'))
                    ->searchable(),
                TextColumn::make('measurement_unit')
                    ->label(\Modules\Core\Support\FilamentUi::field('measurement_unit'))
                    ->searchable(),
                TextColumn::make('target_type')
                    ->label(\Modules\Core\Support\FilamentUi::field('target_type'))
                    ->searchable(),
                TextColumn::make('target_value')
                    ->label(\Modules\Core\Support\FilamentUi::field('target_value'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('target_minimum')
                    ->label(\Modules\Core\Support\FilamentUi::field('target_minimum'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('target_maximum')
                    ->label(\Modules\Core\Support\FilamentUi::field('target_maximum'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('weight_percentage')
                    ->label(\Modules\Core\Support\FilamentUi::field('weight_percentage'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('scoring_method')
                    ->label(\Modules\Core\Support\FilamentUi::field('scoring_method'))
                    ->searchable(),
                TextColumn::make('data_source')
                    ->label(\Modules\Core\Support\FilamentUi::field('data_source'))
                    ->searchable(),
                IconColumn::make('is_active')
                    ->label(\Modules\Core\Support\FilamentUi::field('is_active'))
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
