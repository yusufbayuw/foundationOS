<?php

namespace Modules\School\Filament\Resources\Schedules\Tables;

use App\Filament\Imports\ScheduleImporter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ImportTableActions;
use Modules\Core\Support\FilamentUi;

class SchedulesTable
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
                TextColumn::make('academicPeriod.name')
                    ->label(FilamentUi::field('academicPeriod.name'))
                    ->searchable(),
                TextColumn::make('class_id')
                    ->label(FilamentUi::field('class_id'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('subject.name')
                    ->label(FilamentUi::field('subject.name'))
                    ->searchable(),
                TextColumn::make('teacher.id')
                    ->label(FilamentUi::field('teacher.id'))
                    ->searchable(),
                TextColumn::make('day_of_week')
                    ->label(FilamentUi::field('day_of_week'))
                    ->searchable(),
                TextColumn::make('start_time')
                    ->label(FilamentUi::field('start_time'))
                    ->time()
                    ->sortable(),
                TextColumn::make('end_time')
                    ->label(FilamentUi::field('end_time'))
                    ->time()
                    ->sortable(),
                TextColumn::make('duration_minutes')
                    ->label(FilamentUi::field('duration_minutes'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('schedule_type')
                    ->label(FilamentUi::field('schedule_type'))
                    ->searchable(),
                TextColumn::make('semester')
                    ->label(FilamentUi::field('semester'))
                    ->searchable(),
                IconColumn::make('is_recurring')
                    ->label(FilamentUi::field('is_recurring'))
                    ->boolean(),
                TextColumn::make('effective_date')
                    ->label(FilamentUi::field('effective_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('expiry_date')
                    ->label(FilamentUi::field('expiry_date'))
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
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->headerActions([
                ...ImportTableActions::make(ScheduleImporter::class),
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
