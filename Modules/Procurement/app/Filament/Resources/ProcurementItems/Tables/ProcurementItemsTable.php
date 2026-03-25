<?php

namespace Modules\Procurement\Filament\Resources\ProcurementItems\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProcurementItemsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('tenant.name'))
                    ->searchable(),
                TextColumn::make('category.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('category.name'))
                    ->searchable(),
                TextColumn::make('preferredVendor.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('preferredVendor.name'))
                    ->searchable(),
                TextColumn::make('chartOfAccount.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('chartOfAccount.name'))
                    ->searchable(),
                TextColumn::make('code')
                    ->label(\Modules\Core\Support\FilamentUi::field('code'))
                    ->searchable(),
                TextColumn::make('name')
                    ->label(\Modules\Core\Support\FilamentUi::field('name'))
                    ->searchable(),
                TextColumn::make('unit_of_measure')
                    ->label(\Modules\Core\Support\FilamentUi::field('unit_of_measure'))
                    ->searchable(),
                TextColumn::make('estimated_price')
                    ->label(\Modules\Core\Support\FilamentUi::field('estimated_price'))
                    ->money()
                    ->sortable(),
                TextColumn::make('last_purchase_price')
                    ->label(\Modules\Core\Support\FilamentUi::field('last_purchase_price'))
                    ->money()
                    ->sortable(),
                TextColumn::make('minimum_order_quantity')
                    ->label(\Modules\Core\Support\FilamentUi::field('minimum_order_quantity'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('lead_time_days')
                    ->label(\Modules\Core\Support\FilamentUi::field('lead_time_days'))
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
