<?php

namespace Modules\School\Filament\Resources\Subjects\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\TrashedFilter;
use Modules\Core\Filament\Support\ImportTableActions;

class SubjectsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('tenant.name'))
                    ->searchable(),
                TextColumn::make('organization.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('organization.name'))
                    ->searchable(),
                TextColumn::make('curriculum.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('curriculum.name'))
                    ->searchable(),
                TextColumn::make('name')
                    ->label(\Modules\Core\Support\FilamentUi::field('name'))
                    ->searchable(),
                TextColumn::make('short_name')
                    ->label(\Modules\Core\Support\FilamentUi::field('short_name'))
                    ->searchable(),
                TextColumn::make('code')
                    ->label(\Modules\Core\Support\FilamentUi::field('code'))
                    ->searchable(),
                TextColumn::make('grade_level')
                    ->label(\Modules\Core\Support\FilamentUi::field('grade_level'))
                    ->searchable(),
                TextColumn::make('credits')
                    ->label(\Modules\Core\Support\FilamentUi::field('credits'))
                    ->numeric()
                    ->sortable(),
                IconColumn::make('is_mandatory')
                    ->label(\Modules\Core\Support\FilamentUi::field('is_mandatory'))
                    ->boolean(),
                TextColumn::make('subject_group')
                    ->label(\Modules\Core\Support\FilamentUi::field('subject_group'))
                    ->searchable(),
                IconColumn::make('has_practicum')
                    ->label(\Modules\Core\Support\FilamentUi::field('has_practicum'))
                    ->boolean(),
                TextColumn::make('color_code')
                    ->label(\Modules\Core\Support\FilamentUi::field('color_code'))
                    ->searchable(),
                TextColumn::make('icon')
                    ->label(\Modules\Core\Support\FilamentUi::field('icon'))
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
                ...ImportTableActions::make(\App\Filament\Imports\SubjectImporter::class),
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
