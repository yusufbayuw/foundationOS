<?php

namespace Modules\Campus\Filament\Resources\Courses\Tables;

use App\Filament\Imports\CourseImporter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ImportTableActions;
use Modules\Core\Support\FilamentUi;

class CoursesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->label(FilamentUi::field('tenant.name'))
                    ->searchable(),
                TextColumn::make('studyProgram.name')
                    ->label(FilamentUi::field('studyProgram.name'))
                    ->searchable(),
                TextColumn::make('code')
                    ->label(FilamentUi::field('code'))
                    ->searchable(),
                TextColumn::make('name')
                    ->label(FilamentUi::field('name'))
                    ->searchable(),
                TextColumn::make('credits')
                    ->label(FilamentUi::field('credits'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('theory_credits')
                    ->label(FilamentUi::field('theory_credits'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('practicum_credits')
                    ->label(FilamentUi::field('practicum_credits'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('semester_level')
                    ->label(FilamentUi::field('semester_level'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('course_type')
                    ->label(FilamentUi::field('course_type'))
                    ->searchable(),
                IconColumn::make('is_mandatory')
                    ->label(FilamentUi::field('is_mandatory'))
                    ->boolean(),
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
                SelectFilter::make('course_type')
                    ->options([
                        'mandatory' => 'Mandatory',
                        'elective' => 'Elective',
                        'practicum' => 'Practicum',
                    ]),
                SelectFilter::make('semester_level')
                    ->options([
                        '1' => 'Semester 1',
                        '2' => 'Semester 2',
                        '3' => 'Semester 3',
                        '4' => 'Semester 4',
                        '5' => 'Semester 5',
                        '6' => 'Semester 6',
                        '7' => 'Semester 7',
                        '8' => 'Semester 8',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->headerActions([
                ...ImportTableActions::make(CourseImporter::class),
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
