<?php

namespace Modules\Employee\Filament\Resources\KpiIndicators\Tables;

use App\Filament\Imports\KpiIndicatorImporter;
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

class KpiIndicatorsTable
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
                TextColumn::make('code')
                    ->label(FilamentUi::field('code'))
                    ->searchable(),
                TextColumn::make('name')
                    ->label(FilamentUi::field('name'))
                    ->searchable(),
                TextColumn::make('category')
                    ->label(FilamentUi::field('category'))
                    ->searchable(),
                TextColumn::make('measurement_unit')
                    ->label(FilamentUi::field('measurement_unit'))
                    ->searchable(),
                TextColumn::make('target_type')
                    ->label(FilamentUi::field('target_type'))
                    ->searchable(),
                TextColumn::make('target_value')
                    ->label(FilamentUi::field('target_value'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('target_minimum')
                    ->label(FilamentUi::field('target_minimum'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('target_maximum')
                    ->label(FilamentUi::field('target_maximum'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('weight_percentage')
                    ->label(FilamentUi::field('weight_percentage'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('scoring_method')
                    ->label(FilamentUi::field('scoring_method'))
                    ->searchable(),
                TextColumn::make('data_source')
                    ->label(FilamentUi::field('data_source'))
                    ->searchable(),
                IconColumn::make('is_active')
                    ->label(FilamentUi::field('is_active'))
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
                ...ImportTableActions::make(KpiIndicatorImporter::class),
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
