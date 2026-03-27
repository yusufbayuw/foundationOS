<?php

namespace Modules\Campus\Filament\Resources\Courses\Tables;

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

class CoursesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('tenant.name'))
                    ->searchable(),
                TextColumn::make('studyProgram.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('studyProgram.name'))
                    ->searchable(),
                TextColumn::make('code')
                    ->label(\Modules\Core\Support\FilamentUi::field('code'))
                    ->searchable(),
                TextColumn::make('name')
                    ->label(\Modules\Core\Support\FilamentUi::field('name'))
                    ->searchable(),
                TextColumn::make('credits')
                    ->label(\Modules\Core\Support\FilamentUi::field('credits'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('theory_credits')
                    ->label(\Modules\Core\Support\FilamentUi::field('theory_credits'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('practicum_credits')
                    ->label(\Modules\Core\Support\FilamentUi::field('practicum_credits'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('semester_level')
                    ->label(\Modules\Core\Support\FilamentUi::field('semester_level'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('course_type')
                    ->label(\Modules\Core\Support\FilamentUi::field('course_type'))
                    ->searchable(),
                IconColumn::make('is_mandatory')
                    ->label(\Modules\Core\Support\FilamentUi::field('is_mandatory'))
                    ->boolean(),
                IconColumn::make('is_active')
                    ->label(\Modules\Core\Support\FilamentUi::field('is_active'))
                    ->boolean(),
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
                ...ImportTableActions::make(\App\Filament\Imports\CourseImporter::class),
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
