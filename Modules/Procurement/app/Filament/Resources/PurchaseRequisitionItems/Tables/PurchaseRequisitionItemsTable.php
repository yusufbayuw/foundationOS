<?php

namespace Modules\Procurement\Filament\Resources\PurchaseRequisitionItems\Tables;

use App\Filament\Imports\PurchaseRequisitionItemImporter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ImportTableActions;
use Modules\Core\Support\FilamentUi;

class PurchaseRequisitionItemsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->label(FilamentUi::field('tenant.name'))
                    ->searchable(),
                TextColumn::make('purchase_requisition_id')
                    ->label(FilamentUi::field('purchase_requisition_id'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('procurementItem.name')
                    ->label(FilamentUi::field('procurementItem.name'))
                    ->searchable(),
                TextColumn::make('preferredVendor.name')
                    ->label(FilamentUi::field('preferredVendor.name'))
                    ->searchable(),
                TextColumn::make('department.name')
                    ->label(FilamentUi::field('department.name'))
                    ->searchable(),
                TextColumn::make('purchaseOrder.id')
                    ->label(FilamentUi::field('purchaseOrder.id'))
                    ->searchable(),
                TextColumn::make('quantity_requested')
                    ->label(FilamentUi::field('quantity_requested'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('unit_of_measure')
                    ->label(FilamentUi::field('unit_of_measure'))
                    ->searchable(),
                TextColumn::make('estimated_unit_price')
                    ->label(FilamentUi::field('estimated_unit_price'))
                    ->money()
                    ->sortable(),
                TextColumn::make('estimated_total_price')
                    ->label(FilamentUi::field('estimated_total_price'))
                    ->money()
                    ->sortable(),
                TextColumn::make('required_date')
                    ->label(FilamentUi::field('required_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('budgetAccount.name')
                    ->label(FilamentUi::field('budgetAccount.name'))
                    ->searchable(),
                TextColumn::make('status')
                    ->label(FilamentUi::field('status'))
                    ->searchable(),
                TextColumn::make('ordered_quantity')
                    ->label(FilamentUi::field('ordered_quantity'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('received_quantity')
                    ->label(FilamentUi::field('received_quantity'))
                    ->numeric()
                    ->sortable(),
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
                ...ImportTableActions::make(PurchaseRequisitionItemImporter::class),
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
