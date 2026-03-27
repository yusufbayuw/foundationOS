<?php

namespace Modules\Campus\Filament\Resources\StudyResults\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\TrashedFilter;
use Modules\Core\Filament\Support\ImportTableActions;

class StudyResultsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('tenant.name'))
                    ->searchable(),
                TextColumn::make('studyPlanItem.id')
                    ->label(\Modules\Core\Support\FilamentUi::field('studyPlanItem.id'))
                    ->searchable(),
                TextColumn::make('grade_letter')
                    ->label(\Modules\Core\Support\FilamentUi::field('grade_letter'))
                    ->searchable(),
                TextColumn::make('grade_point')
                    ->label(\Modules\Core\Support\FilamentUi::field('grade_point'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('weight_score')
                    ->label(\Modules\Core\Support\FilamentUi::field('weight_score'))
                    ->numeric()
                    ->sortable(),
                IconColumn::make('passed')
                    ->label(\Modules\Core\Support\FilamentUi::field('passed'))
                    ->boolean(),
                TextColumn::make('published_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('published_at'))
                    ->dateTime()
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
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->headerActions([
                ...ImportTableActions::make(\App\Filament\Imports\StudyResultImporter::class),
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
