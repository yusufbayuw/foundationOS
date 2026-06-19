<?php

namespace Modules\Procurement\Filament\Resources\VendorBills\Tables;

use App\Filament\Imports\VendorBillImporter;
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

class VendorBillsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->label(FilamentUi::field('tenant.name'))
                    ->searchable(),
                TextColumn::make('vendor.name')
                    ->label(FilamentUi::field('vendor.name'))
                    ->searchable(),
                TextColumn::make('purchaseOrder.id')
                    ->label(FilamentUi::field('purchaseOrder.id'))
                    ->searchable(),
                TextColumn::make('goodsReceipt.id')
                    ->label(FilamentUi::field('goodsReceipt.id'))
                    ->searchable(),
                TextColumn::make('journalEntry.id')
                    ->label(FilamentUi::field('journalEntry.id'))
                    ->searchable(),
                TextColumn::make('processed_by')
                    ->label(FilamentUi::field('processed_by'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('bill_number')
                    ->label(FilamentUi::field('bill_number'))
                    ->searchable(),
                TextColumn::make('bill_date')
                    ->label(FilamentUi::field('bill_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('due_date')
                    ->label(FilamentUi::field('due_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('reference_number')
                    ->label(FilamentUi::field('reference_number'))
                    ->searchable(),
                TextColumn::make('subtotal')
                    ->label(FilamentUi::field('subtotal'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('discount_amount')
                    ->label(FilamentUi::field('discount_amount'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('tax_amount')
                    ->label(FilamentUi::field('tax_amount'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('other_costs')
                    ->label(FilamentUi::field('other_costs'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total_amount')
                    ->label(FilamentUi::field('total_amount'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('amount_paid')
                    ->label(FilamentUi::field('amount_paid'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('status')
                    ->label(FilamentUi::field('status'))
                    ->searchable(),
                TextColumn::make('payment_status')
                    ->label(FilamentUi::field('payment_status'))
                    ->searchable(),
                TextColumn::make('processed_at')
                    ->label(FilamentUi::field('processed_at'))
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
                ...ImportTableActions::make(VendorBillImporter::class),
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
