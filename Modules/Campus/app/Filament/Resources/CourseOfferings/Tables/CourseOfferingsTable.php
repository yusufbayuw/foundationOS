<?php

namespace Modules\Campus\Filament\Resources\CourseOfferings\Tables;

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

class CourseOfferingsTable
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
                TextColumn::make('course.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('course.name'))
                    ->searchable(),
                TextColumn::make('academicPeriod.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('academicPeriod.name'))
                    ->searchable(),
                TextColumn::make('lecturer.id')
                    ->label(\Modules\Core\Support\FilamentUi::field('lecturer.id'))
                    ->searchable(),
                TextColumn::make('class_code')
                    ->label(\Modules\Core\Support\FilamentUi::field('class_code'))
                    ->searchable(),
                TextColumn::make('capacity')
                    ->label(\Modules\Core\Support\FilamentUi::field('capacity'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('enrolled_count')
                    ->label(\Modules\Core\Support\FilamentUi::field('enrolled_count'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('delivery_mode')
                    ->label(\Modules\Core\Support\FilamentUi::field('delivery_mode'))
                    ->searchable(),
                TextColumn::make('day_of_week')
                    ->label(\Modules\Core\Support\FilamentUi::field('day_of_week'))
                    ->searchable(),
                TextColumn::make('start_time')
                    ->label(\Modules\Core\Support\FilamentUi::field('start_time'))
                    ->searchable(),
                TextColumn::make('end_time')
                    ->label(\Modules\Core\Support\FilamentUi::field('end_time'))
                    ->searchable(),
                TextColumn::make('room_name')
                    ->label(\Modules\Core\Support\FilamentUi::field('room_name'))
                    ->searchable(),
                TextColumn::make('status')
                    ->label(\Modules\Core\Support\FilamentUi::field('status'))
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
                ...ImportTableActions::make(\App\Filament\Imports\CourseOfferingImporter::class),
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
