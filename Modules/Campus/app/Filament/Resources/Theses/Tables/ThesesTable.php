<?php

namespace Modules\Campus\Filament\Resources\Theses\Tables;

use App\Filament\Imports\ThesisImporter;
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

class ThesesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->label(FilamentUi::field('tenant.name'))
                    ->searchable(),
                TextColumn::make('collageStudent.id')
                    ->label(FilamentUi::field('collageStudent.id'))
                    ->searchable(),
                TextColumn::make('advisorLecturer.id')
                    ->label(FilamentUi::field('advisorLecturer.id'))
                    ->searchable(),
                TextColumn::make('examinerLecturer.id')
                    ->label(FilamentUi::field('examinerLecturer.id'))
                    ->searchable(),
                TextColumn::make('title')
                    ->label(FilamentUi::field('title'))
                    ->searchable(),
                TextColumn::make('research_area')
                    ->label(FilamentUi::field('research_area'))
                    ->searchable(),
                TextColumn::make('proposal_submitted_at')
                    ->label(FilamentUi::field('proposal_submitted_at'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('defense_date')
                    ->label(FilamentUi::field('defense_date'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('status')
                    ->label(FilamentUi::field('status'))
                    ->searchable(),
                TextColumn::make('grade_letter')
                    ->label(FilamentUi::field('grade_letter'))
                    ->searchable(),
                TextColumn::make('grade_point')
                    ->label(FilamentUi::field('grade_point'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('document_path')
                    ->label(FilamentUi::field('document_path'))
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
                ...ImportTableActions::make(ThesisImporter::class),
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
