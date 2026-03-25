<?php

namespace Modules\School\Filament\Resources\AssessmentItems\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AssessmentItemsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('tenant.name'))
                    ->searchable(),
                TextColumn::make('assessment.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('assessment.name'))
                    ->searchable(),
                TextColumn::make('item_type')
                    ->label(\Modules\Core\Support\FilamentUi::field('item_type'))
                    ->searchable(),
                TextColumn::make('question_number')
                    ->label(\Modules\Core\Support\FilamentUi::field('question_number'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('question_attachment')
                    ->label(\Modules\Core\Support\FilamentUi::field('question_attachment'))
                    ->searchable(),
                TextColumn::make('max_score')
                    ->label(\Modules\Core\Support\FilamentUi::field('max_score'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('weight')
                    ->label(\Modules\Core\Support\FilamentUi::field('weight'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('difficulty_level')
                    ->label(\Modules\Core\Support\FilamentUi::field('difficulty_level'))
                    ->searchable(),
                TextColumn::make('cognitive_level')
                    ->label(\Modules\Core\Support\FilamentUi::field('cognitive_level'))
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
