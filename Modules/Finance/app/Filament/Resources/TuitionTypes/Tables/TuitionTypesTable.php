<?php

namespace Modules\Finance\Filament\Resources\TuitionTypes\Tables;

use App\Filament\Imports\TuitionTypeImporter;
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

class TuitionTypesTable
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
                TextColumn::make('code')
                    ->label(FilamentUi::field('code'))
                    ->searchable(),
                TextColumn::make('name')
                    ->label(FilamentUi::field('name'))
                    ->searchable(),
                TextColumn::make('education_level')
                    ->label(FilamentUi::field('education_level'))
                    ->searchable(),
                TextColumn::make('amount')
                    ->label(FilamentUi::field('amount'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('frequency')
                    ->label(FilamentUi::field('frequency'))
                    ->searchable(),
                TextColumn::make('due_day')
                    ->label(FilamentUi::field('due_day'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('grace_period_days')
                    ->label(FilamentUi::field('grace_period_days'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('late_fee_percentage')
                    ->label(FilamentUi::field('late_fee_percentage'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('late_fee_fixed')
                    ->label(FilamentUi::field('late_fee_fixed'))
                    ->numeric()
                    ->sortable(),
                IconColumn::make('discount_eligible')
                    ->label(FilamentUi::field('discount_eligible'))
                    ->boolean(),
                IconColumn::make('is_active')
                    ->label(FilamentUi::field('is_active'))
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
                ...ImportTableActions::make(TuitionTypeImporter::class),
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
