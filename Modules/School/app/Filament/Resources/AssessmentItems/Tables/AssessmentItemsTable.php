<?php

namespace Modules\School\Filament\Resources\AssessmentItems\Tables;

use App\Filament\Imports\AssessmentItemImporter;
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

class AssessmentItemsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->label(FilamentUi::field('tenant.name'))
                    ->searchable(),
                TextColumn::make('assessment.name')
                    ->label(FilamentUi::field('assessment.name'))
                    ->searchable(),
                TextColumn::make('item_type')
                    ->label(FilamentUi::field('item_type'))
                    ->searchable(),
                TextColumn::make('question_number')
                    ->label(FilamentUi::field('question_number'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('question_attachment')
                    ->label(FilamentUi::field('question_attachment'))
                    ->searchable(),
                TextColumn::make('max_score')
                    ->label(FilamentUi::field('max_score'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('weight')
                    ->label(FilamentUi::field('weight'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('difficulty_level')
                    ->label(FilamentUi::field('difficulty_level'))
                    ->searchable(),
                TextColumn::make('cognitive_level')
                    ->label(FilamentUi::field('cognitive_level'))
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
                ...ImportTableActions::make(AssessmentItemImporter::class),
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
