<?php

namespace Modules\Procurement\Filament\Resources\VendorBillItems\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\TrashedFilter;
use Modules\Core\Filament\Support\ImportTableActions;

class VendorBillItemsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('tenant.name'))
                    ->searchable(),
                TextColumn::make('vendorBill.id')
                    ->label(\Modules\Core\Support\FilamentUi::field('vendorBill.id'))
                    ->searchable(),
                TextColumn::make('purchaseOrderItem.id')
                    ->label(\Modules\Core\Support\FilamentUi::field('purchaseOrderItem.id'))
                    ->searchable(),
                TextColumn::make('goodsReceiptItem.id')
                    ->label(\Modules\Core\Support\FilamentUi::field('goodsReceiptItem.id'))
                    ->searchable(),
                TextColumn::make('quantity')
                    ->label(\Modules\Core\Support\FilamentUi::field('quantity'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('unit_of_measure')
                    ->label(\Modules\Core\Support\FilamentUi::field('unit_of_measure'))
                    ->searchable(),
                TextColumn::make('unit_price')
                    ->label(\Modules\Core\Support\FilamentUi::field('unit_price'))
                    ->money()
                    ->sortable(),
                TextColumn::make('tax_amount')
                    ->label(\Modules\Core\Support\FilamentUi::field('tax_amount'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('line_total')
                    ->label(\Modules\Core\Support\FilamentUi::field('line_total'))
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
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->headerActions([
                ...ImportTableActions::make(\App\Filament\Imports\VendorBillItemImporter::class),
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
