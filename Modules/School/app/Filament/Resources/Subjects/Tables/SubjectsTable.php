<?php

namespace Modules\School\Filament\Resources\Subjects\Tables;

use App\Filament\Imports\SubjectImporter;
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

class SubjectsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->label(FilamentUi::field('tenant.name'))
                    ->searchable(),
                TextColumn::make('organization.name')
                    ->label(FilamentUi::field('organization.name'))
                    ->searchable(),
                TextColumn::make('curriculum.name')
                    ->label(FilamentUi::field('curriculum.name'))
                    ->searchable(),
                TextColumn::make('name')
                    ->label(FilamentUi::field('name'))
                    ->searchable(),
                TextColumn::make('short_name')
                    ->label(FilamentUi::field('short_name'))
                    ->searchable(),
                TextColumn::make('code')
                    ->label(FilamentUi::field('code'))
                    ->searchable(),
                TextColumn::make('grade_level')
                    ->label(FilamentUi::field('grade_level'))
                    ->searchable(),
                TextColumn::make('credits')
                    ->label(FilamentUi::field('credits'))
                    ->numeric()
                    ->sortable(),
                IconColumn::make('is_mandatory')
                    ->label(FilamentUi::field('is_mandatory'))
                    ->boolean(),
                TextColumn::make('subject_group')
                    ->label(FilamentUi::field('subject_group'))
                    ->searchable(),
                IconColumn::make('has_practicum')
                    ->label(FilamentUi::field('has_practicum'))
                    ->boolean(),
                TextColumn::make('color_code')
                    ->label(FilamentUi::field('color_code'))
                    ->searchable(),
                TextColumn::make('icon')
                    ->label(FilamentUi::field('icon'))
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
                ...ImportTableActions::make(SubjectImporter::class),
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
