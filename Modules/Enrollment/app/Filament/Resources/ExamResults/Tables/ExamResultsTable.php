<?php

namespace Modules\Enrollment\Filament\Resources\ExamResults\Tables;

use App\Filament\Imports\ExamResultImporter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ImportTableActions;
use Modules\Core\Support\FilamentUi;

class ExamResultsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->label(FilamentUi::field('tenant.name'))
                    ->searchable(),
                TextColumn::make('applicant.id')
                    ->label(FilamentUi::field('applicant.id'))
                    ->searchable(),
                TextColumn::make('examiner.name')
                    ->label(FilamentUi::field('examiner.name'))
                    ->searchable(),
                TextColumn::make('seat_number')
                    ->label(FilamentUi::field('seat_number'))
                    ->searchable(),
                TextColumn::make('score')
                    ->label(FilamentUi::field('score'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('grade')
                    ->label(FilamentUi::field('grade'))
                    ->searchable(),
                IconColumn::make('is_passed')
                    ->label(FilamentUi::field('is_passed'))
                    ->boolean(),
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
                TextColumn::make('examSchedule.name')
                    ->label(FilamentUi::field('examSchedule.name'))
                    ->searchable(),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->headerActions([
                ...ImportTableActions::make(ExamResultImporter::class),
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
