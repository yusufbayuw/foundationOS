<?php

namespace Modules\Enrollment\Filament\Resources\ExamResults\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ExamResultsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('tenant.name'))
                    ->searchable(),
                TextColumn::make('applicant.id')
                    ->label(\Modules\Core\Support\FilamentUi::field('applicant.id'))
                    ->searchable(),
                TextColumn::make('examiner.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('examiner.name'))
                    ->searchable(),
                TextColumn::make('seat_number')
                    ->label(\Modules\Core\Support\FilamentUi::field('seat_number'))
                    ->searchable(),
                TextColumn::make('score')
                    ->label(\Modules\Core\Support\FilamentUi::field('score'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('grade')
                    ->label(\Modules\Core\Support\FilamentUi::field('grade'))
                    ->searchable(),
                IconColumn::make('is_passed')
                    ->label(\Modules\Core\Support\FilamentUi::field('is_passed'))
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
                TextColumn::make('examSchedule.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('examSchedule.name'))
                    ->searchable(),
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
