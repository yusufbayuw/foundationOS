<?php

namespace Modules\School\Filament\Resources\StudentRiskScores\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Modules\Core\Support\FilamentUi;

class StudentRiskScoresTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant_id')
                    ->label(FilamentUi::field('tenant_id'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('student_id')
                    ->label(FilamentUi::field('student_id'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('composite_score')
                    ->label(FilamentUi::field('composite_score'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('academic_score')
                    ->label(FilamentUi::field('academic_score'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('financial_score')
                    ->label(FilamentUi::field('financial_score'))
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
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
