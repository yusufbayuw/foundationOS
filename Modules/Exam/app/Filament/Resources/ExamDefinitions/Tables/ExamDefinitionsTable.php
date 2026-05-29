<?php

namespace Modules\Exam\Filament\Resources\ExamDefinitions\Tables;

use App\Filament\Imports\ExamDefinitionImporter;
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

class ExamDefinitionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(FilamentUi::field('name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('code')
                    ->label(FilamentUi::field('code'))
                    ->searchable(),
                TextColumn::make('exam_academic_context')
                    ->label(FilamentUi::field('exam_academic_context'))
                    ->badge(),
                TextColumn::make('exam_type')
                    ->label(FilamentUi::field('exam_type'))
                    ->badge(),
                TextColumn::make('exam_purpose')
                    ->label(FilamentUi::field('exam_purpose'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('status')
                    ->label(FilamentUi::field('status'))
                    ->badge(),
                TextColumn::make('starts_at')
                    ->label(FilamentUi::field('starts_at'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('ends_at')
                    ->label(FilamentUi::field('ends_at'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->headerActions([
                ...ImportTableActions::make(ExamDefinitionImporter::class),
            ]);
    }
}
