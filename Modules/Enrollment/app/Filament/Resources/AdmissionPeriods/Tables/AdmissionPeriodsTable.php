<?php

namespace Modules\Enrollment\Filament\Resources\AdmissionPeriods\Tables;

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

class AdmissionPeriodsTable
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
                TextColumn::make('name')
                    ->label(\Modules\Core\Support\FilamentUi::field('name'))
                    ->searchable(),
                TextColumn::make('code')
                    ->label(\Modules\Core\Support\FilamentUi::field('code'))
                    ->searchable(),
                TextColumn::make('type')
                    ->label(\Modules\Core\Support\FilamentUi::field('type'))
                    ->searchable(),
                TextColumn::make('start_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('start_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('end_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('end_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('announcement_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('announcement_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('registration_fee')
                    ->label(\Modules\Core\Support\FilamentUi::field('registration_fee'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('quota')
                    ->label(\Modules\Core\Support\FilamentUi::field('quota'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('registered_count')
                    ->label(\Modules\Core\Support\FilamentUi::field('registered_count'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('accepted_count')
                    ->label(\Modules\Core\Support\FilamentUi::field('accepted_count'))
                    ->numeric()
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label(\Modules\Core\Support\FilamentUi::field('is_active'))
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
                ...ImportTableActions::make(\App\Filament\Imports\AdmissionPeriodImporter::class),
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
