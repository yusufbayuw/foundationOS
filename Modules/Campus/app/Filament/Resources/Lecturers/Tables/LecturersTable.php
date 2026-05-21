<?php

namespace Modules\Campus\Filament\Resources\Lecturers\Tables;

use App\Filament\Imports\LecturerImporter;
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

class LecturersTable
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
                TextColumn::make('nidn')
                    ->label(FilamentUi::field('nidn'))
                    ->searchable(),
                TextColumn::make('employee_number')
                    ->label(FilamentUi::field('employee_number'))
                    ->searchable(),
                TextColumn::make('full_name')
                    ->label(FilamentUi::field('full_name'))
                    ->searchable(),
                TextColumn::make('academic_title_prefix')
                    ->label(FilamentUi::field('academic_title_prefix'))
                    ->searchable(),
                TextColumn::make('academic_title_suffix')
                    ->label(FilamentUi::field('academic_title_suffix'))
                    ->searchable(),
                TextColumn::make('functional_position')
                    ->label(FilamentUi::field('functional_position'))
                    ->searchable(),
                TextColumn::make('employment_status')
                    ->label(FilamentUi::field('employment_status'))
                    ->searchable(),
                TextColumn::make('email')
                    ->label(FilamentUi::text('Email address'))
                    ->searchable(),
                TextColumn::make('phone')
                    ->label(FilamentUi::field('phone'))
                    ->searchable(),
                TextColumn::make('join_date')
                    ->label(FilamentUi::field('join_date'))
                    ->date()
                    ->sortable(),
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
                SelectFilter::make('employment_status')
                    ->options([
                        'permanent' => 'Permanent',
                        'contract' => 'Contract',
                        'part_time' => 'Part Time',
                        'honorary' => 'Honorary',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->headerActions([
                ...ImportTableActions::make(LecturerImporter::class),
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
