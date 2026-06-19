<?php

namespace Modules\School\Filament\Resources\ClassStudents\Tables;

use App\Filament\Imports\ClassStudentImporter;
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

class ClassStudentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->label(FilamentUi::field('tenant.name'))
                    ->searchable(),
                TextColumn::make('academicPeriod.name')
                    ->label(FilamentUi::field('academicPeriod.name'))
                    ->searchable(),
                TextColumn::make('class_id')
                    ->label(FilamentUi::field('class_id'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('student.id')
                    ->label(FilamentUi::field('student.id'))
                    ->searchable(),
                TextColumn::make('entry_date')
                    ->label(FilamentUi::field('entry_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('exit_date')
                    ->label(FilamentUi::field('exit_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('status')
                    ->label(FilamentUi::field('status'))
                    ->searchable(),
                TextColumn::make('entry_type')
                    ->label(FilamentUi::field('entry_type'))
                    ->searchable(),
                TextColumn::make('ranking')
                    ->label(FilamentUi::field('ranking'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('certificate_number')
                    ->label(FilamentUi::field('certificate_number'))
                    ->searchable(),
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
                ...ImportTableActions::make(ClassStudentImporter::class),
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
