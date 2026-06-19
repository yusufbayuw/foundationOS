<?php

namespace Modules\Campus\Filament\Resources\StudyResults\Tables;

use App\Filament\Imports\StudyResultImporter;
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

class StudyResultsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->label(FilamentUi::field('tenant.name'))
                    ->searchable(),
                TextColumn::make('studyPlanItem.id')
                    ->label(FilamentUi::field('studyPlanItem.id'))
                    ->searchable(),
                TextColumn::make('grade_letter')
                    ->label(FilamentUi::field('grade_letter'))
                    ->searchable(),
                TextColumn::make('grade_point')
                    ->label(FilamentUi::field('grade_point'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('weight_score')
                    ->label(FilamentUi::field('weight_score'))
                    ->numeric()
                    ->sortable(),
                IconColumn::make('passed')
                    ->label(FilamentUi::field('passed'))
                    ->boolean(),
                TextColumn::make('published_at')
                    ->label(FilamentUi::field('published_at'))
                    ->dateTime()
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
                ...ImportTableActions::make(StudyResultImporter::class),
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
