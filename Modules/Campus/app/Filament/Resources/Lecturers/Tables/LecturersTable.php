<?php

namespace Modules\Campus\Filament\Resources\Lecturers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LecturersTable
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
                TextColumn::make('nidn')
                    ->label(\Modules\Core\Support\FilamentUi::field('nidn'))
                    ->searchable(),
                TextColumn::make('employee_number')
                    ->label(\Modules\Core\Support\FilamentUi::field('employee_number'))
                    ->searchable(),
                TextColumn::make('full_name')
                    ->label(\Modules\Core\Support\FilamentUi::field('full_name'))
                    ->searchable(),
                TextColumn::make('academic_title_prefix')
                    ->label(\Modules\Core\Support\FilamentUi::field('academic_title_prefix'))
                    ->searchable(),
                TextColumn::make('academic_title_suffix')
                    ->label(\Modules\Core\Support\FilamentUi::field('academic_title_suffix'))
                    ->searchable(),
                TextColumn::make('functional_position')
                    ->label(\Modules\Core\Support\FilamentUi::field('functional_position'))
                    ->searchable(),
                TextColumn::make('employment_status')
                    ->label(\Modules\Core\Support\FilamentUi::field('employment_status'))
                    ->searchable(),
                TextColumn::make('email')
                    ->label(\Modules\Core\Support\FilamentUi::text('Email address'))
                    ->searchable(),
                TextColumn::make('phone')
                    ->label(\Modules\Core\Support\FilamentUi::field('phone'))
                    ->searchable(),
                TextColumn::make('join_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('join_date'))
                    ->date()
                    ->sortable(),
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
