<?php

namespace Modules\School\Filament\Resources\StudentAchievements\Tables;

use App\Filament\Imports\StudentAchievementImporter;
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

class StudentAchievementsTable
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
                TextColumn::make('academicYear.name')
                    ->label(FilamentUi::field('academicYear.name'))
                    ->searchable(),
                TextColumn::make('student.id')
                    ->label(FilamentUi::field('student.id'))
                    ->searchable(),
                TextColumn::make('achievementType.name')
                    ->label(FilamentUi::field('achievementType.name'))
                    ->searchable(),
                TextColumn::make('verified_by')
                    ->label(FilamentUi::field('verified_by'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('title')
                    ->label(FilamentUi::field('title'))
                    ->searchable(),
                TextColumn::make('event_name')
                    ->label(FilamentUi::field('event_name'))
                    ->searchable(),
                TextColumn::make('event_date')
                    ->label(FilamentUi::field('event_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('event_location')
                    ->label(FilamentUi::field('event_location'))
                    ->searchable(),
                TextColumn::make('organizer')
                    ->label(FilamentUi::field('organizer'))
                    ->searchable(),
                TextColumn::make('rank_position')
                    ->label(FilamentUi::field('rank_position'))
                    ->searchable(),
                TextColumn::make('certificate_number')
                    ->label(FilamentUi::field('certificate_number'))
                    ->searchable(),
                TextColumn::make('certificate_file')
                    ->label(FilamentUi::field('certificate_file'))
                    ->searchable(),
                TextColumn::make('news_link')
                    ->label(FilamentUi::field('news_link'))
                    ->searchable(),
                TextColumn::make('points_earned')
                    ->label(FilamentUi::field('points_earned'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('verified_at')
                    ->label(FilamentUi::field('verified_at'))
                    ->dateTime()
                    ->sortable(),
                IconColumn::make('is_featured')
                    ->label(FilamentUi::field('is_featured'))
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
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->headerActions([
                ...ImportTableActions::make(StudentAchievementImporter::class),
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
