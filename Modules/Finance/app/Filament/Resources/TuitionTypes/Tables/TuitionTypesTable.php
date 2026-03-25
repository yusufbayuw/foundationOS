<?php

namespace Modules\Finance\Filament\Resources\TuitionTypes\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TuitionTypesTable
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
                TextColumn::make('code')
                    ->label(\Modules\Core\Support\FilamentUi::field('code'))
                    ->searchable(),
                TextColumn::make('name')
                    ->label(\Modules\Core\Support\FilamentUi::field('name'))
                    ->searchable(),
                TextColumn::make('education_level')
                    ->label(\Modules\Core\Support\FilamentUi::field('education_level'))
                    ->searchable(),
                TextColumn::make('amount')
                    ->label(\Modules\Core\Support\FilamentUi::field('amount'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('frequency')
                    ->label(\Modules\Core\Support\FilamentUi::field('frequency'))
                    ->searchable(),
                TextColumn::make('due_day')
                    ->label(\Modules\Core\Support\FilamentUi::field('due_day'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('grace_period_days')
                    ->label(\Modules\Core\Support\FilamentUi::field('grace_period_days'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('late_fee_percentage')
                    ->label(\Modules\Core\Support\FilamentUi::field('late_fee_percentage'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('late_fee_fixed')
                    ->label(\Modules\Core\Support\FilamentUi::field('late_fee_fixed'))
                    ->numeric()
                    ->sortable(),
                IconColumn::make('discount_eligible')
                    ->label(\Modules\Core\Support\FilamentUi::field('discount_eligible'))
                    ->boolean(),
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
            ->filters([])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                                                        ]),
            ]);
    }
}
