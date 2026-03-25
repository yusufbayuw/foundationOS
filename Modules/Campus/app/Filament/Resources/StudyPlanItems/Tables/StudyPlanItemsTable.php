<?php

namespace Modules\Campus\Filament\Resources\StudyPlanItems\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StudyPlanItemsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('tenant.name'))
                    ->searchable(),
                TextColumn::make('studyPlan.id')
                    ->label(\Modules\Core\Support\FilamentUi::field('studyPlan.id'))
                    ->searchable(),
                TextColumn::make('courseOffering.id')
                    ->label(\Modules\Core\Support\FilamentUi::field('courseOffering.id'))
                    ->searchable(),
                TextColumn::make('course.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('course.name'))
                    ->searchable(),
                TextColumn::make('credits')
                    ->label(\Modules\Core\Support\FilamentUi::field('credits'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('status')
                    ->label(\Modules\Core\Support\FilamentUi::field('status'))
                    ->searchable(),
                TextColumn::make('grade_letter')
                    ->label(\Modules\Core\Support\FilamentUi::field('grade_letter'))
                    ->searchable(),
                TextColumn::make('grade_point')
                    ->label(\Modules\Core\Support\FilamentUi::field('grade_point'))
                    ->numeric()
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
