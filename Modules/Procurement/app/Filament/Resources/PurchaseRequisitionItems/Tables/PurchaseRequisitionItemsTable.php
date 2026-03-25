<?php

namespace Modules\Procurement\Filament\Resources\PurchaseRequisitionItems\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PurchaseRequisitionItemsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('tenant.name'))
                    ->searchable(),
                TextColumn::make('purchase_requisition_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('purchase_requisition_id'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('procurementItem.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('procurementItem.name'))
                    ->searchable(),
                TextColumn::make('preferredVendor.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('preferredVendor.name'))
                    ->searchable(),
                TextColumn::make('department.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('department.name'))
                    ->searchable(),
                TextColumn::make('purchaseOrder.id')
                    ->label(\Modules\Core\Support\FilamentUi::field('purchaseOrder.id'))
                    ->searchable(),
                TextColumn::make('quantity_requested')
                    ->label(\Modules\Core\Support\FilamentUi::field('quantity_requested'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('unit_of_measure')
                    ->label(\Modules\Core\Support\FilamentUi::field('unit_of_measure'))
                    ->searchable(),
                TextColumn::make('estimated_unit_price')
                    ->label(\Modules\Core\Support\FilamentUi::field('estimated_unit_price'))
                    ->money()
                    ->sortable(),
                TextColumn::make('estimated_total_price')
                    ->label(\Modules\Core\Support\FilamentUi::field('estimated_total_price'))
                    ->money()
                    ->sortable(),
                TextColumn::make('required_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('required_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('budgetAccount.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('budgetAccount.name'))
                    ->searchable(),
                TextColumn::make('status')
                    ->label(\Modules\Core\Support\FilamentUi::field('status'))
                    ->searchable(),
                TextColumn::make('ordered_quantity')
                    ->label(\Modules\Core\Support\FilamentUi::field('ordered_quantity'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('received_quantity')
                    ->label(\Modules\Core\Support\FilamentUi::field('received_quantity'))
                    ->numeric()
                    ->sortable(),
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
