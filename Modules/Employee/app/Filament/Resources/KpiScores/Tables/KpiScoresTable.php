<?php

namespace Modules\Employee\Filament\Resources\KpiScores\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\TrashedFilter;
use Modules\Core\Filament\Support\ImportTableActions;

class KpiScoresTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('tenant.name'))
                    ->searchable(),
                TextColumn::make('employee.id')
                    ->label(\Modules\Core\Support\FilamentUi::field('employee.id'))
                    ->searchable(),
                TextColumn::make('kpi_template_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('kpi_template_id'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('evaluator.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('evaluator.name'))
                    ->searchable(),
                TextColumn::make('period_month')
                    ->label(\Modules\Core\Support\FilamentUi::field('period_month'))
                    ->searchable(),
                TextColumn::make('period_year')
                    ->label(\Modules\Core\Support\FilamentUi::field('period_year'))
                    ->searchable(),
                TextColumn::make('total_score')
                    ->label(\Modules\Core\Support\FilamentUi::field('total_score'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('grade')
                    ->label(\Modules\Core\Support\FilamentUi::field('grade'))
                    ->searchable(),
                TextColumn::make('status')
                    ->label(\Modules\Core\Support\FilamentUi::field('status'))
                    ->searchable(),
                TextColumn::make('submitted_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('submitted_at'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('evaluated_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('evaluated_at'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('approved_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('approved_at'))
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
                ...ImportTableActions::make(\App\Filament\Imports\KpiScoreImporter::class),
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
