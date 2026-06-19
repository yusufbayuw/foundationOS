<?php

namespace Modules\Procurement\Filament\Resources\GoodsReceiptItems\Tables;

use App\Filament\Imports\GoodsReceiptItemImporter;
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

class GoodsReceiptItemsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->label(FilamentUi::field('tenant.name'))
                    ->searchable(),
                TextColumn::make('goodsReceipt.id')
                    ->label(FilamentUi::field('goodsReceipt.id'))
                    ->searchable(),
                TextColumn::make('purchaseOrderItem.id')
                    ->label(FilamentUi::field('purchaseOrderItem.id'))
                    ->searchable(),
                TextColumn::make('quantity_received')
                    ->label(FilamentUi::field('quantity_received'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('quantity_accepted')
                    ->label(FilamentUi::field('quantity_accepted'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('quantity_rejected')
                    ->label(FilamentUi::field('quantity_rejected'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('unit_price')
                    ->label(FilamentUi::field('unit_price'))
                    ->money()
                    ->sortable(),
                TextColumn::make('total_amount')
                    ->label(FilamentUi::field('total_amount'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('condition_status')
                    ->label(FilamentUi::field('condition_status'))
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
                ...ImportTableActions::make(GoodsReceiptItemImporter::class),
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
