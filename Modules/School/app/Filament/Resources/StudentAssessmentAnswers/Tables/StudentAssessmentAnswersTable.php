<?php

namespace Modules\School\Filament\Resources\StudentAssessmentAnswers\Tables;

use App\Filament\Imports\StudentAssessmentAnswerImporter;
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

class StudentAssessmentAnswersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->label(FilamentUi::field('tenant.name'))
                    ->searchable(),
                TextColumn::make('assessment.name')
                    ->label(FilamentUi::field('assessment.name'))
                    ->searchable(),
                TextColumn::make('assessmentItem.id')
                    ->label(FilamentUi::field('assessmentItem.id'))
                    ->searchable(),
                TextColumn::make('student.id')
                    ->label(FilamentUi::field('student.id'))
                    ->searchable(),
                TextColumn::make('classStudent.id')
                    ->label(FilamentUi::field('classStudent.id'))
                    ->searchable(),
                TextColumn::make('graded_by')
                    ->label(FilamentUi::field('graded_by'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('answer_selected')
                    ->label(FilamentUi::field('answer_selected'))
                    ->searchable(),
                TextColumn::make('answer_attachment')
                    ->label(FilamentUi::field('answer_attachment'))
                    ->searchable(),
                TextColumn::make('score')
                    ->label(FilamentUi::field('score'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('max_score')
                    ->label(FilamentUi::field('max_score'))
                    ->numeric()
                    ->sortable(),
                IconColumn::make('is_correct')
                    ->label(FilamentUi::field('is_correct'))
                    ->boolean(),
                TextColumn::make('graded_at')
                    ->label(FilamentUi::field('graded_at'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('attempt_number')
                    ->label(FilamentUi::field('attempt_number'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('time_spent_seconds')
                    ->label(FilamentUi::field('time_spent_seconds'))
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
                ...ImportTableActions::make(StudentAssessmentAnswerImporter::class),
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
