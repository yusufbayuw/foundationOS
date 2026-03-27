<?php

namespace Modules\School\Filament\Resources\ClassStudents\Tables;

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

class ClassStudentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('tenant.name'))
                    ->searchable(),
                TextColumn::make('academicPeriod.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('academicPeriod.name'))
                    ->searchable(),
                TextColumn::make('class_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('class_id'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('student.id')
                    ->label(\Modules\Core\Support\FilamentUi::field('student.id'))
                    ->searchable(),
                TextColumn::make('entry_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('entry_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('exit_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('exit_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('status')
                    ->label(\Modules\Core\Support\FilamentUi::field('status'))
                    ->searchable(),
                TextColumn::make('entry_type')
                    ->label(\Modules\Core\Support\FilamentUi::field('entry_type'))
                    ->searchable(),
                TextColumn::make('ranking')
                    ->label(\Modules\Core\Support\FilamentUi::field('ranking'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('certificate_number')
                    ->label(\Modules\Core\Support\FilamentUi::field('certificate_number'))
                    ->searchable(),
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
                ...ImportTableActions::make(\App\Filament\Imports\ClassStudentImporter::class),
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
