<?php

namespace Modules\Procurement\Filament\Resources\ProcurementItems\Tables;

use App\Filament\Imports\ProcurementItemImporter;
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

class ProcurementItemsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->label(FilamentUi::field('tenant.name'))
                    ->searchable(),
                TextColumn::make('category.name')
                    ->label(FilamentUi::field('category.name'))
                    ->searchable(),
                TextColumn::make('preferredVendor.name')
                    ->label(FilamentUi::field('preferredVendor.name'))
                    ->searchable(),
                TextColumn::make('chartOfAccount.name')
                    ->label(FilamentUi::field('chartOfAccount.name'))
                    ->searchable(),
                TextColumn::make('code')
                    ->label(FilamentUi::field('code'))
                    ->searchable(),
                TextColumn::make('name')
                    ->label(FilamentUi::field('name'))
                    ->searchable(),
                TextColumn::make('unit_of_measure')
                    ->label(FilamentUi::field('unit_of_measure'))
                    ->searchable(),
                TextColumn::make('estimated_price')
                    ->label(FilamentUi::field('estimated_price'))
                    ->money()
                    ->sortable(),
                TextColumn::make('last_purchase_price')
                    ->label(FilamentUi::field('last_purchase_price'))
                    ->money()
                    ->sortable(),
                TextColumn::make('minimum_order_quantity')
                    ->label(FilamentUi::field('minimum_order_quantity'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('lead_time_days')
                    ->label(FilamentUi::field('lead_time_days'))
                    ->numeric()
                    ->sortable(),
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
                ...ImportTableActions::make(ProcurementItemImporter::class),
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
