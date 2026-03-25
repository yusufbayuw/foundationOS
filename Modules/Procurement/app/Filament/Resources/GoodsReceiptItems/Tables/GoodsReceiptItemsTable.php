<?php

namespace Modules\Procurement\Filament\Resources\GoodsReceiptItems\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class GoodsReceiptItemsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('tenant.name'))
                    ->searchable(),
                TextColumn::make('goodsReceipt.id')
                    ->label(\Modules\Core\Support\FilamentUi::field('goodsReceipt.id'))
                    ->searchable(),
                TextColumn::make('purchaseOrderItem.id')
                    ->label(\Modules\Core\Support\FilamentUi::field('purchaseOrderItem.id'))
                    ->searchable(),
                TextColumn::make('quantity_received')
                    ->label(\Modules\Core\Support\FilamentUi::field('quantity_received'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('quantity_accepted')
                    ->label(\Modules\Core\Support\FilamentUi::field('quantity_accepted'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('quantity_rejected')
                    ->label(\Modules\Core\Support\FilamentUi::field('quantity_rejected'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('unit_price')
                    ->label(\Modules\Core\Support\FilamentUi::field('unit_price'))
                    ->money()
                    ->sortable(),
                TextColumn::make('total_amount')
                    ->label(\Modules\Core\Support\FilamentUi::field('total_amount'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('condition_status')
                    ->label(\Modules\Core\Support\FilamentUi::field('condition_status'))
                    ->searchable(),
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
