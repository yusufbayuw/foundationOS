<?php

namespace Modules\Campus\Filament\Resources\CourseOfferings\Tables;

use App\Filament\Imports\CourseOfferingImporter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ImportTableActions;
use Modules\Core\Support\FilamentUi;

class CourseOfferingsTable
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
                TextColumn::make('course.name')
                    ->label(FilamentUi::field('course.name'))
                    ->searchable(),
                TextColumn::make('academicPeriod.name')
                    ->label(FilamentUi::field('academicPeriod.name'))
                    ->searchable(),
                TextColumn::make('lecturer.id')
                    ->label(FilamentUi::field('lecturer.id'))
                    ->searchable(),
                TextColumn::make('class_code')
                    ->label(FilamentUi::field('class_code'))
                    ->searchable(),
                TextColumn::make('capacity')
                    ->label(FilamentUi::field('capacity'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('enrolled_count')
                    ->label(FilamentUi::field('enrolled_count'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('delivery_mode')
                    ->label(FilamentUi::field('delivery_mode'))
                    ->searchable(),
                TextColumn::make('day_of_week')
                    ->label(FilamentUi::field('day_of_week'))
                    ->searchable(),
                TextColumn::make('start_time')
                    ->label(FilamentUi::field('start_time'))
                    ->searchable(),
                TextColumn::make('end_time')
                    ->label(FilamentUi::field('end_time'))
                    ->searchable(),
                TextColumn::make('room_name')
                    ->label(FilamentUi::field('room_name'))
                    ->searchable(),
                TextColumn::make('status')
                    ->label(FilamentUi::field('status'))
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
                SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'open' => 'Open',
                        'closed' => 'Closed',
                        'cancelled' => 'Cancelled',
                    ]),
                SelectFilter::make('delivery_mode')
                    ->options([
                        'offline' => 'Offline',
                        'online' => 'Online',
                        'hybrid' => 'Hybrid',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->headerActions([
                ...ImportTableActions::make(CourseOfferingImporter::class),
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
