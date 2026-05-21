<?php

namespace Modules\Campus\Filament\Resources\CollageStudents\Tables;

use App\Filament\Imports\CollageStudentImporter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ImportTableActions;
use Modules\Core\Support\FilamentUi;

class CollageStudentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->label(FilamentUi::field('tenant.name'))
                    ->searchable(),
                TextColumn::make('organization.name')
                    ->label(FilamentUi::field('organization.name'))
                    ->searchable(),
                TextColumn::make('user.name')
                    ->label(FilamentUi::field('user.name'))
                    ->searchable(),
                TextColumn::make('studyProgram.name')
                    ->label(FilamentUi::field('studyProgram.name'))
                    ->searchable(),
                TextColumn::make('academicAdvisor.id')
                    ->label(FilamentUi::field('academicAdvisor.id'))
                    ->searchable(),
                TextColumn::make('student_number')
                    ->label(FilamentUi::field('student_number'))
                    ->searchable(),
                TextColumn::make('national_student_number')
                    ->label(FilamentUi::field('national_student_number'))
                    ->searchable(),
                TextColumn::make('full_name')
                    ->label(FilamentUi::field('full_name'))
                    ->searchable(),
                TextColumn::make('entry_year')
                    ->label(FilamentUi::field('entry_year'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('entry_semester')
                    ->label(FilamentUi::field('entry_semester'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('admission_type')
                    ->label(FilamentUi::field('admission_type'))
                    ->searchable(),
                TextColumn::make('current_semester')
                    ->label(FilamentUi::field('current_semester'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('status')
                    ->label(FilamentUi::field('status'))
                    ->searchable(),
                TextColumn::make('email')
                    ->label(FilamentUi::text('Email address'))
                    ->searchable(),
                TextColumn::make('phone')
                    ->label(FilamentUi::field('phone'))
                    ->searchable(),
                TextColumn::make('graduation_date')
                    ->label(FilamentUi::field('graduation_date'))
                    ->date()
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
                SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'graduated' => 'Graduated',
                        'dropped_out' => 'Dropped Out',
                        'on_leave' => 'On Leave',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->headerActions([
                ...ImportTableActions::make(CollageStudentImporter::class),
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
