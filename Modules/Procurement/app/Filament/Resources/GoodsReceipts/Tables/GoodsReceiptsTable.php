<?php

namespace Modules\Procurement\Filament\Resources\GoodsReceipts\Tables;

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

class GoodsReceiptsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('tenant.name'))
                    ->searchable(),
                TextColumn::make('purchaseOrder.id')
                    ->label(\Modules\Core\Support\FilamentUi::field('purchaseOrder.id'))
                    ->searchable(),
                TextColumn::make('received_by')
                    ->label(\Modules\Core\Support\FilamentUi::field('received_by'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('inspected_by')
                    ->label(\Modules\Core\Support\FilamentUi::field('inspected_by'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('receipt_number')
                    ->label(\Modules\Core\Support\FilamentUi::field('receipt_number'))
                    ->searchable(),
                TextColumn::make('receipt_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('receipt_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('delivery_note_number')
                    ->label(\Modules\Core\Support\FilamentUi::field('delivery_note_number'))
                    ->searchable(),
                TextColumn::make('supplier_delivery_number')
                    ->label(\Modules\Core\Support\FilamentUi::field('supplier_delivery_number'))
                    ->searchable(),
                TextColumn::make('status')
                    ->label(\Modules\Core\Support\FilamentUi::field('status'))
                    ->searchable(),
                TextColumn::make('received_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('received_at'))
                    ->dateTime()
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
                ...ImportTableActions::make(\App\Filament\Imports\GoodsReceiptImporter::class),
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
