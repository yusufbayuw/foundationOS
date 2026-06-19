<?php

namespace Modules\School\Filament\Resources\Assessments\Tables;

use App\Filament\Imports\AssessmentImporter;
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

class AssessmentsTable
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
                TextColumn::make('academicPeriod.name')
                    ->label(FilamentUi::field('academicPeriod.name'))
                    ->searchable(),
                TextColumn::make('subject.name')
                    ->label(FilamentUi::field('subject.name'))
                    ->searchable(),
                TextColumn::make('class_id')
                    ->label(FilamentUi::field('class_id'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('name')
                    ->label(FilamentUi::field('name'))
                    ->searchable(),
                TextColumn::make('code')
                    ->label(FilamentUi::field('code'))
                    ->searchable(),
                TextColumn::make('type')
                    ->label(FilamentUi::field('type'))
                    ->searchable(),
                TextColumn::make('assessment_category')
                    ->label(FilamentUi::field('assessment_category'))
                    ->searchable(),
                TextColumn::make('weight')
                    ->label(FilamentUi::field('weight'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('max_score')
                    ->label(FilamentUi::field('max_score'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('passing_score')
                    ->label(FilamentUi::field('passing_score'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('schedule_date')
                    ->label(FilamentUi::field('schedule_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('start_time')
                    ->label(FilamentUi::field('start_time'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('end_time')
                    ->label(FilamentUi::field('end_time'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('duration_minutes')
                    ->label(FilamentUi::field('duration_minutes'))
                    ->numeric()
                    ->sortable(),
                IconColumn::make('is_published')
                    ->label(FilamentUi::field('is_published'))
                    ->boolean(),
                TextColumn::make('published_at')
                    ->label(FilamentUi::field('published_at'))
                    ->dateTime()
                    ->sortable(),
                IconColumn::make('allow_retake')
                    ->label(FilamentUi::field('allow_retake'))
                    ->boolean(),
                TextColumn::make('max_attempts')
                    ->label(FilamentUi::field('max_attempts'))
                    ->numeric()
                    ->sortable(),
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
                ...ImportTableActions::make(AssessmentImporter::class),
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
