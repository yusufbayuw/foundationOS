<?php

namespace Modules\School\Filament\Resources\StudentGrades\Tables;

use App\Filament\Imports\StudentGradeImporter;
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

class StudentGradesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->label(FilamentUi::field('tenant.name'))
                    ->searchable(),
                TextColumn::make('student.id')
                    ->label(FilamentUi::field('student.id'))
                    ->searchable(),
                TextColumn::make('assessment.name')
                    ->label(FilamentUi::field('assessment.name'))
                    ->searchable(),
                TextColumn::make('graded_by')
                    ->label(FilamentUi::field('graded_by'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('score')
                    ->label(FilamentUi::field('score'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('score_letter')
                    ->label(FilamentUi::field('score_letter'))
                    ->searchable(),
                TextColumn::make('weight')
                    ->label(FilamentUi::field('weight'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('final_score')
                    ->label(FilamentUi::field('final_score'))
                    ->numeric()
                    ->sortable(),
                IconColumn::make('is_passed')
                    ->label(FilamentUi::field('is_passed'))
                    ->boolean(),
                TextColumn::make('graded_at')
                    ->label(FilamentUi::field('graded_at'))
                    ->dateTime()
                    ->sortable(),
                IconColumn::make('is_locked')
                    ->label(FilamentUi::field('is_locked'))
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
                ...ImportTableActions::make(StudentGradeImporter::class),
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
