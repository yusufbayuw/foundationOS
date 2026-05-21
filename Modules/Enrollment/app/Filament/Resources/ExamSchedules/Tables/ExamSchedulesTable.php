<?php

namespace Modules\Enrollment\Filament\Resources\ExamSchedules\Tables;

use App\Filament\Imports\ExamScheduleImporter;
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

class ExamSchedulesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->label(FilamentUi::field('tenant.name'))
                    ->searchable(),
                TextColumn::make('admissionPeriod.name')
                    ->label(FilamentUi::field('admissionPeriod.name'))
                    ->searchable(),
                TextColumn::make('name')
                    ->label(FilamentUi::field('name'))
                    ->searchable(),
                TextColumn::make('type')
                    ->label(FilamentUi::field('type'))
                    ->searchable(),
                TextColumn::make('date')
                    ->label(FilamentUi::field('date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('start_time')
                    ->label(FilamentUi::field('start_time'))
                    ->time()
                    ->sortable(),
                TextColumn::make('end_time')
                    ->label(FilamentUi::field('end_time'))
                    ->time()
                    ->sortable(),
                TextColumn::make('location')
                    ->label(FilamentUi::field('location'))
                    ->searchable(),
                TextColumn::make('room_capacity')
                    ->label(FilamentUi::field('room_capacity'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('registered_count')
                    ->label(FilamentUi::field('registered_count'))
                    ->numeric()
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
                SelectFilter::make('type')
                    ->options([
                        'test' => 'Test',
                        'interview' => 'Interview',
                        'written' => 'Written',
                        'practical' => 'Practical',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->headerActions([
                ...ImportTableActions::make(ExamScheduleImporter::class),
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
