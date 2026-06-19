<?php

namespace Modules\Employee\Filament\Resources\AttendanceLogs\Tables;

use App\Filament\Imports\AttendanceLogImporter;
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

class AttendanceLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->label(FilamentUi::field('tenant.name'))
                    ->searchable(),
                TextColumn::make('employee.id')
                    ->label(FilamentUi::field('employee.id'))
                    ->searchable(),
                TextColumn::make('shift.name')
                    ->label(FilamentUi::field('shift.name'))
                    ->searchable(),
                TextColumn::make('approved_by')
                    ->label(FilamentUi::field('approved_by'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('date')
                    ->label(FilamentUi::field('date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('check_in')
                    ->label(FilamentUi::field('check_in'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('check_out')
                    ->label(FilamentUi::field('check_out'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('work_hours')
                    ->label(FilamentUi::field('work_hours'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('overtime_hours')
                    ->label(FilamentUi::field('overtime_hours'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('status')
                    ->label(FilamentUi::field('status'))
                    ->searchable(),
                TextColumn::make('device_check_in')
                    ->label(FilamentUi::field('device_check_in'))
                    ->searchable(),
                TextColumn::make('device_check_out')
                    ->label(FilamentUi::field('device_check_out'))
                    ->searchable(),
                TextColumn::make('photo_check_in')
                    ->label(FilamentUi::field('photo_check_in'))
                    ->searchable(),
                TextColumn::make('photo_check_out')
                    ->label(FilamentUi::field('photo_check_out'))
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
                ...ImportTableActions::make(AttendanceLogImporter::class),
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
