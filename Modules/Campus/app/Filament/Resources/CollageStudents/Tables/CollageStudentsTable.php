<?php

namespace Modules\Campus\Filament\Resources\CollageStudents\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CollageStudentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('tenant.name'))
                    ->searchable(),
                TextColumn::make('organization.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('organization.name'))
                    ->searchable(),
                TextColumn::make('user.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('user.name'))
                    ->searchable(),
                TextColumn::make('studyProgram.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('studyProgram.name'))
                    ->searchable(),
                TextColumn::make('academicAdvisor.id')
                    ->label(\Modules\Core\Support\FilamentUi::field('academicAdvisor.id'))
                    ->searchable(),
                TextColumn::make('student_number')
                    ->label(\Modules\Core\Support\FilamentUi::field('student_number'))
                    ->searchable(),
                TextColumn::make('national_student_number')
                    ->label(\Modules\Core\Support\FilamentUi::field('national_student_number'))
                    ->searchable(),
                TextColumn::make('full_name')
                    ->label(\Modules\Core\Support\FilamentUi::field('full_name'))
                    ->searchable(),
                TextColumn::make('entry_year')
                    ->label(\Modules\Core\Support\FilamentUi::field('entry_year'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('entry_semester')
                    ->label(\Modules\Core\Support\FilamentUi::field('entry_semester'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('admission_type')
                    ->label(\Modules\Core\Support\FilamentUi::field('admission_type'))
                    ->searchable(),
                TextColumn::make('current_semester')
                    ->label(\Modules\Core\Support\FilamentUi::field('current_semester'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('status')
                    ->label(\Modules\Core\Support\FilamentUi::field('status'))
                    ->searchable(),
                TextColumn::make('email')
                    ->label(\Modules\Core\Support\FilamentUi::text('Email address'))
                    ->searchable(),
                TextColumn::make('phone')
                    ->label(\Modules\Core\Support\FilamentUi::field('phone'))
                    ->searchable(),
                TextColumn::make('graduation_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('graduation_date'))
                    ->date()
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
