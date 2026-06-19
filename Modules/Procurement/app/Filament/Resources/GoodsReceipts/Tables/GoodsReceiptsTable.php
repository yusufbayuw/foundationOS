<?php

namespace Modules\Procurement\Filament\Resources\GoodsReceipts\Tables;

use App\Filament\Imports\GoodsReceiptImporter;
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

class GoodsReceiptsTable
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
                TextColumn::make('received_by')
                    ->label(FilamentUi::field('received_by'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('inspected_by')
                    ->label(FilamentUi::field('inspected_by'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('receipt_number')
                    ->label(FilamentUi::field('receipt_number'))
                    ->searchable(),
                TextColumn::make('receipt_date')
                    ->label(FilamentUi::field('receipt_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('delivery_note_number')
                    ->label(FilamentUi::field('delivery_note_number'))
                    ->searchable(),
                TextColumn::make('supplier_delivery_number')
                    ->label(FilamentUi::field('supplier_delivery_number'))
                    ->searchable(),
                TextColumn::make('status')
                    ->label(FilamentUi::field('status'))
                    ->searchable(),
                TextColumn::make('received_at')
                    ->label(FilamentUi::field('received_at'))
                    ->dateTime()
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
                ...ImportTableActions::make(GoodsReceiptImporter::class),
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
