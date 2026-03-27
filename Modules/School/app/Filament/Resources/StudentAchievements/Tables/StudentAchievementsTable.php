<?php

namespace Modules\School\Filament\Resources\StudentAchievements\Tables;

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

class StudentAchievementsTable
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
                TextColumn::make('academicYear.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('academicYear.name'))
                    ->searchable(),
                TextColumn::make('student.id')
                    ->label(\Modules\Core\Support\FilamentUi::field('student.id'))
                    ->searchable(),
                TextColumn::make('achievementType.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('achievementType.name'))
                    ->searchable(),
                TextColumn::make('verified_by')
                    ->label(\Modules\Core\Support\FilamentUi::field('verified_by'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('title')
                    ->label(\Modules\Core\Support\FilamentUi::field('title'))
                    ->searchable(),
                TextColumn::make('event_name')
                    ->label(\Modules\Core\Support\FilamentUi::field('event_name'))
                    ->searchable(),
                TextColumn::make('event_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('event_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('event_location')
                    ->label(\Modules\Core\Support\FilamentUi::field('event_location'))
                    ->searchable(),
                TextColumn::make('organizer')
                    ->label(\Modules\Core\Support\FilamentUi::field('organizer'))
                    ->searchable(),
                TextColumn::make('rank_position')
                    ->label(\Modules\Core\Support\FilamentUi::field('rank_position'))
                    ->searchable(),
                TextColumn::make('certificate_number')
                    ->label(\Modules\Core\Support\FilamentUi::field('certificate_number'))
                    ->searchable(),
                TextColumn::make('certificate_file')
                    ->label(\Modules\Core\Support\FilamentUi::field('certificate_file'))
                    ->searchable(),
                TextColumn::make('news_link')
                    ->label(\Modules\Core\Support\FilamentUi::field('news_link'))
                    ->searchable(),
                TextColumn::make('points_earned')
                    ->label(\Modules\Core\Support\FilamentUi::field('points_earned'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('verified_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('verified_at'))
                    ->dateTime()
                    ->sortable(),
                IconColumn::make('is_featured')
                    ->label(\Modules\Core\Support\FilamentUi::field('is_featured'))
                    ->boolean(),
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
                ...ImportTableActions::make(\App\Filament\Imports\StudentAchievementImporter::class),
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
