<?php

namespace Modules\School\Filament\Resources\StudentAssessmentAnswers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StudentAssessmentAnswersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('tenant.name'))
                    ->searchable(),
                TextColumn::make('assessment.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('assessment.name'))
                    ->searchable(),
                TextColumn::make('assessmentItem.id')
                    ->label(\Modules\Core\Support\FilamentUi::field('assessmentItem.id'))
                    ->searchable(),
                TextColumn::make('student.id')
                    ->label(\Modules\Core\Support\FilamentUi::field('student.id'))
                    ->searchable(),
                TextColumn::make('classStudent.id')
                    ->label(\Modules\Core\Support\FilamentUi::field('classStudent.id'))
                    ->searchable(),
                TextColumn::make('graded_by')
                    ->label(\Modules\Core\Support\FilamentUi::field('graded_by'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('answer_selected')
                    ->label(\Modules\Core\Support\FilamentUi::field('answer_selected'))
                    ->searchable(),
                TextColumn::make('answer_attachment')
                    ->label(\Modules\Core\Support\FilamentUi::field('answer_attachment'))
                    ->searchable(),
                TextColumn::make('score')
                    ->label(\Modules\Core\Support\FilamentUi::field('score'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('max_score')
                    ->label(\Modules\Core\Support\FilamentUi::field('max_score'))
                    ->numeric()
                    ->sortable(),
                IconColumn::make('is_correct')
                    ->label(\Modules\Core\Support\FilamentUi::field('is_correct'))
                    ->boolean(),
                TextColumn::make('graded_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('graded_at'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('attempt_number')
                    ->label(\Modules\Core\Support\FilamentUi::field('attempt_number'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('time_spent_seconds')
                    ->label(\Modules\Core\Support\FilamentUi::field('time_spent_seconds'))
                    ->numeric()
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
