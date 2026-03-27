<?php

namespace Modules\School\Filament\Resources\Assessments\Tables;

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

class AssessmentsTable
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
                TextColumn::make('academicPeriod.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('academicPeriod.name'))
                    ->searchable(),
                TextColumn::make('subject.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('subject.name'))
                    ->searchable(),
                TextColumn::make('class_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('class_id'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('name')
                    ->label(\Modules\Core\Support\FilamentUi::field('name'))
                    ->searchable(),
                TextColumn::make('code')
                    ->label(\Modules\Core\Support\FilamentUi::field('code'))
                    ->searchable(),
                TextColumn::make('type')
                    ->label(\Modules\Core\Support\FilamentUi::field('type'))
                    ->searchable(),
                TextColumn::make('assessment_category')
                    ->label(\Modules\Core\Support\FilamentUi::field('assessment_category'))
                    ->searchable(),
                TextColumn::make('weight')
                    ->label(\Modules\Core\Support\FilamentUi::field('weight'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('max_score')
                    ->label(\Modules\Core\Support\FilamentUi::field('max_score'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('passing_score')
                    ->label(\Modules\Core\Support\FilamentUi::field('passing_score'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('schedule_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('schedule_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('start_time')
                    ->label(\Modules\Core\Support\FilamentUi::field('start_time'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('end_time')
                    ->label(\Modules\Core\Support\FilamentUi::field('end_time'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('duration_minutes')
                    ->label(\Modules\Core\Support\FilamentUi::field('duration_minutes'))
                    ->numeric()
                    ->sortable(),
                IconColumn::make('is_published')
                    ->label(\Modules\Core\Support\FilamentUi::field('is_published'))
                    ->boolean(),
                TextColumn::make('published_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('published_at'))
                    ->dateTime()
                    ->sortable(),
                IconColumn::make('allow_retake')
                    ->label(\Modules\Core\Support\FilamentUi::field('allow_retake'))
                    ->boolean(),
                TextColumn::make('max_attempts')
                    ->label(\Modules\Core\Support\FilamentUi::field('max_attempts'))
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
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->headerActions([
                ...ImportTableActions::make(\App\Filament\Imports\AssessmentImporter::class),
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
