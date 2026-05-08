<?php

namespace Modules\Employee\Filament\Resources\AttendanceLogs\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ImportTableActions;
use Modules\Employee\Enums\AttendanceStatus;

class AttendanceLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('date', 'desc')
            ->columns([
                TextColumn::make('employee.full_name')
                    ->label('Karyawan')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('shift.name')
                    ->label('Shift')
                    ->badge()
                    ->color('gray')
                    ->placeholder('-'),

                TextColumn::make('check_in')
                    ->label('Masuk')
                    ->time('H:i')
                    ->sortable(),

                TextColumn::make('check_out')
                    ->label('Keluar')
                    ->time('H:i')
                    ->sortable(),

                TextColumn::make('work_hours')
                    ->label('Jam Kerja')
                    ->suffix(' jam')
                    ->numeric(2)
                    ->sortable(),

                TextColumn::make('overtime_hours')
                    ->label('Lembur')
                    ->suffix(' jam')
                    ->numeric(2)
                    ->sortable()
                    ->color(fn ($state) => (float) $state > 0 ? 'warning' : null),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (AttendanceStatus $state): string => $state->getColor()),

                TextColumn::make('approvedBy.name')
                    ->label('Disetujui')
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(AttendanceStatus::class),

                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->headerActions([
                ...ImportTableActions::make(\App\Filament\Imports\AttendanceLogImporter::class),
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
