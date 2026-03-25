<?php

namespace Modules\School\Filament\Resources\Schedules\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SchedulesTable
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
                TextColumn::make('academicPeriod.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('academicPeriod.name'))
                    ->searchable(),
                TextColumn::make('class_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('class_id'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('subject.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('subject.name'))
                    ->searchable(),
                TextColumn::make('teacher.id')
                    ->label(\Modules\Core\Support\FilamentUi::field('teacher.id'))
                    ->searchable(),
                TextColumn::make('day_of_week')
                    ->label(\Modules\Core\Support\FilamentUi::field('day_of_week'))
                    ->searchable(),
                TextColumn::make('start_time')
                    ->label(\Modules\Core\Support\FilamentUi::field('start_time'))
                    ->time()
                    ->sortable(),
                TextColumn::make('end_time')
                    ->label(\Modules\Core\Support\FilamentUi::field('end_time'))
                    ->time()
                    ->sortable(),
                TextColumn::make('duration_minutes')
                    ->label(\Modules\Core\Support\FilamentUi::field('duration_minutes'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('schedule_type')
                    ->label(\Modules\Core\Support\FilamentUi::field('schedule_type'))
                    ->searchable(),
                TextColumn::make('semester')
                    ->label(\Modules\Core\Support\FilamentUi::field('semester'))
                    ->searchable(),
                IconColumn::make('is_recurring')
                    ->label(\Modules\Core\Support\FilamentUi::field('is_recurring'))
                    ->boolean(),
                TextColumn::make('effective_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('effective_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('expiry_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('expiry_date'))
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
