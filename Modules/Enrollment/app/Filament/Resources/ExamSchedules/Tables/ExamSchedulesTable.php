<?php

namespace Modules\Enrollment\Filament\Resources\ExamSchedules\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ExamSchedulesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('tenant.name'))
                    ->searchable(),
                TextColumn::make('admissionPeriod.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('admissionPeriod.name'))
                    ->searchable(),
                TextColumn::make('name')
                    ->label(\Modules\Core\Support\FilamentUi::field('name'))
                    ->searchable(),
                TextColumn::make('type')
                    ->label(\Modules\Core\Support\FilamentUi::field('type'))
                    ->searchable(),
                TextColumn::make('date')
                    ->label(\Modules\Core\Support\FilamentUi::field('date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('start_time')
                    ->label(\Modules\Core\Support\FilamentUi::field('start_time'))
                    ->time()
                    ->sortable(),
                TextColumn::make('end_time')
                    ->label(\Modules\Core\Support\FilamentUi::field('end_time'))
                    ->time()
                    ->sortable(),
                TextColumn::make('location')
                    ->label(\Modules\Core\Support\FilamentUi::field('location'))
                    ->searchable(),
                TextColumn::make('room_capacity')
                    ->label(\Modules\Core\Support\FilamentUi::field('room_capacity'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('registered_count')
                    ->label(\Modules\Core\Support\FilamentUi::field('registered_count'))
                    ->numeric()
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
