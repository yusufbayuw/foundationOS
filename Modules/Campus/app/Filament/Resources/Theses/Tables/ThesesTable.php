<?php

namespace Modules\Campus\Filament\Resources\Theses\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\TrashedFilter;
use Modules\Core\Filament\Support\ImportTableActions;

class ThesesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('tenant.name'))
                    ->searchable(),
                TextColumn::make('collageStudent.id')
                    ->label(\Modules\Core\Support\FilamentUi::field('collageStudent.id'))
                    ->searchable(),
                TextColumn::make('advisorLecturer.id')
                    ->label(\Modules\Core\Support\FilamentUi::field('advisorLecturer.id'))
                    ->searchable(),
                TextColumn::make('examinerLecturer.id')
                    ->label(\Modules\Core\Support\FilamentUi::field('examinerLecturer.id'))
                    ->searchable(),
                TextColumn::make('title')
                    ->label(\Modules\Core\Support\FilamentUi::field('title'))
                    ->searchable(),
                TextColumn::make('research_area')
                    ->label(\Modules\Core\Support\FilamentUi::field('research_area'))
                    ->searchable(),
                TextColumn::make('proposal_submitted_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('proposal_submitted_at'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('defense_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('defense_date'))
                    ->dateTime()
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
                TextColumn::make('document_path')
                    ->label(\Modules\Core\Support\FilamentUi::field('document_path'))
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
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->headerActions([
                ...ImportTableActions::make(\App\Filament\Imports\ThesisImporter::class),
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
