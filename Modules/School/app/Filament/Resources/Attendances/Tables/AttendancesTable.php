<?php

namespace Modules\School\Filament\Resources\Attendances\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AttendancesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('tenant.name'))
                    ->searchable(),
                TextColumn::make('schedule.id')
                    ->label(\Modules\Core\Support\FilamentUi::field('schedule.id'))
                    ->searchable(),
                TextColumn::make('student.id')
                    ->label(\Modules\Core\Support\FilamentUi::field('student.id'))
                    ->searchable(),
                TextColumn::make('verified_by')
                    ->label(\Modules\Core\Support\FilamentUi::field('verified_by'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('entity_type')
                    ->label(\Modules\Core\Support\FilamentUi::field('entity_type'))
                    ->searchable(),
                TextColumn::make('status')
                    ->label(\Modules\Core\Support\FilamentUi::field('status'))
                    ->searchable(),
                TextColumn::make('entry_method')
                    ->label(\Modules\Core\Support\FilamentUi::field('entry_method'))
                    ->searchable(),
                TextColumn::make('attendance_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('attendance_date'))
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
                TextColumn::make('photo_proof')
                    ->label(\Modules\Core\Support\FilamentUi::field('photo_proof'))
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
