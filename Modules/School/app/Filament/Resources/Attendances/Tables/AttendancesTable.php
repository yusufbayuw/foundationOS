<?php

namespace Modules\School\Filament\Resources\Attendances\Tables;

use App\Filament\Imports\AttendanceImporter;
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

class AttendancesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->label(FilamentUi::field('tenant.name'))
                    ->searchable(),
                TextColumn::make('schedule.id')
                    ->label(FilamentUi::field('schedule.id'))
                    ->searchable(),
                TextColumn::make('student.id')
                    ->label(FilamentUi::field('student.id'))
                    ->searchable(),
                TextColumn::make('verified_by')
                    ->label(FilamentUi::field('verified_by'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('entity_type')
                    ->label(FilamentUi::field('entity_type'))
                    ->searchable(),
                TextColumn::make('status')
                    ->label(FilamentUi::field('status'))
                    ->searchable(),
                TextColumn::make('entry_method')
                    ->label(FilamentUi::field('entry_method'))
                    ->searchable(),
                TextColumn::make('attendance_date')
                    ->label(FilamentUi::field('attendance_date'))
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
                TextColumn::make('photo_proof')
                    ->label(FilamentUi::field('photo_proof'))
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
                ...ImportTableActions::make(AttendanceImporter::class),
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
