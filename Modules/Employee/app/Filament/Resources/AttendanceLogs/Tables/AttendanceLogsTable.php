<?php

namespace Modules\Employee\Filament\Resources\AttendanceLogs\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AttendanceLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('tenant.name'))
                    ->searchable(),
                TextColumn::make('employee.id')
                    ->label(\Modules\Core\Support\FilamentUi::field('employee.id'))
                    ->searchable(),
                TextColumn::make('shift.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('shift.name'))
                    ->searchable(),
                TextColumn::make('approved_by')
                    ->label(\Modules\Core\Support\FilamentUi::field('approved_by'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('date')
                    ->label(\Modules\Core\Support\FilamentUi::field('date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('check_in')
                    ->label(\Modules\Core\Support\FilamentUi::field('check_in'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('check_out')
                    ->label(\Modules\Core\Support\FilamentUi::field('check_out'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('work_hours')
                    ->label(\Modules\Core\Support\FilamentUi::field('work_hours'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('overtime_hours')
                    ->label(\Modules\Core\Support\FilamentUi::field('overtime_hours'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('status')
                    ->label(\Modules\Core\Support\FilamentUi::field('status'))
                    ->searchable(),
                TextColumn::make('device_check_in')
                    ->label(\Modules\Core\Support\FilamentUi::field('device_check_in'))
                    ->searchable(),
                TextColumn::make('device_check_out')
                    ->label(\Modules\Core\Support\FilamentUi::field('device_check_out'))
                    ->searchable(),
                TextColumn::make('photo_check_in')
                    ->label(\Modules\Core\Support\FilamentUi::field('photo_check_in'))
                    ->searchable(),
                TextColumn::make('photo_check_out')
                    ->label(\Modules\Core\Support\FilamentUi::field('photo_check_out'))
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
