<?php

namespace Modules\Employee\Filament\Resources\KpiScores\Tables;

use App\Filament\Imports\KpiScoreImporter;
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

class KpiScoresTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->label(FilamentUi::field('tenant.name'))
                    ->searchable(),
                TextColumn::make('employee.id')
                    ->label(FilamentUi::field('employee.id'))
                    ->searchable(),
                TextColumn::make('kpi_template_id')
                    ->label(FilamentUi::field('kpi_template_id'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('evaluator.name')
                    ->label(FilamentUi::field('evaluator.name'))
                    ->searchable(),
                TextColumn::make('period_month')
                    ->label(FilamentUi::field('period_month'))
                    ->searchable(),
                TextColumn::make('period_year')
                    ->label(FilamentUi::field('period_year'))
                    ->searchable(),
                TextColumn::make('total_score')
                    ->label(FilamentUi::field('total_score'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('grade')
                    ->label(FilamentUi::field('grade'))
                    ->searchable(),
                TextColumn::make('status')
                    ->label(FilamentUi::field('status'))
                    ->searchable(),
                TextColumn::make('submitted_at')
                    ->label(FilamentUi::field('submitted_at'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('evaluated_at')
                    ->label(FilamentUi::field('evaluated_at'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('approved_at')
                    ->label(FilamentUi::field('approved_at'))
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
                ...ImportTableActions::make(KpiScoreImporter::class),
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
