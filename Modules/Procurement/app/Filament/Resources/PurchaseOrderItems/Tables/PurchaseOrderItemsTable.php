<?php

namespace Modules\Procurement\Filament\Resources\PurchaseOrderItems\Tables;

use App\Filament\Imports\PurchaseOrderItemImporter;
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

class PurchaseOrderItemsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->label(FilamentUi::field('tenant.name'))
                    ->searchable(),
                TextColumn::make('purchaseOrder.id')
                    ->label(FilamentUi::field('purchaseOrder.id'))
                    ->searchable(),
                TextColumn::make('purchaseRequisitionItem.id')
                    ->label(FilamentUi::field('purchaseRequisitionItem.id'))
                    ->searchable(),
                TextColumn::make('procurementItem.name')
                    ->label(FilamentUi::field('procurementItem.name'))
                    ->searchable(),
                TextColumn::make('quantity')
                    ->label(FilamentUi::field('quantity'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('unit_of_measure')
                    ->label(FilamentUi::field('unit_of_measure'))
                    ->searchable(),
                TextColumn::make('unit_price')
                    ->label(FilamentUi::field('unit_price'))
                    ->money()
                    ->sortable(),
                TextColumn::make('discount_amount')
                    ->label(FilamentUi::field('discount_amount'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('tax_percentage')
                    ->label(FilamentUi::field('tax_percentage'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('tax_amount')
                    ->label(FilamentUi::field('tax_amount'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('line_total')
                    ->label(FilamentUi::field('line_total'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('quantity_received')
                    ->label(FilamentUi::field('quantity_received'))
                    ->numeric()
                    ->sortable(),
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
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->headerActions([
                ...ImportTableActions::make(PurchaseOrderItemImporter::class),
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
