<?php

namespace Modules\Procurement\Filament\Resources\VendorBills\Tables;

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

class VendorBillsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('tenant.name'))
                    ->searchable(),
                TextColumn::make('vendor.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('vendor.name'))
                    ->searchable(),
                TextColumn::make('purchaseOrder.id')
                    ->label(\Modules\Core\Support\FilamentUi::field('purchaseOrder.id'))
                    ->searchable(),
                TextColumn::make('goodsReceipt.id')
                    ->label(\Modules\Core\Support\FilamentUi::field('goodsReceipt.id'))
                    ->searchable(),
                TextColumn::make('journalEntry.id')
                    ->label(\Modules\Core\Support\FilamentUi::field('journalEntry.id'))
                    ->searchable(),
                TextColumn::make('processed_by')
                    ->label(\Modules\Core\Support\FilamentUi::field('processed_by'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('bill_number')
                    ->label(\Modules\Core\Support\FilamentUi::field('bill_number'))
                    ->searchable(),
                TextColumn::make('bill_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('bill_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('due_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('due_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('reference_number')
                    ->label(\Modules\Core\Support\FilamentUi::field('reference_number'))
                    ->searchable(),
                TextColumn::make('subtotal')
                    ->label(\Modules\Core\Support\FilamentUi::field('subtotal'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('discount_amount')
                    ->label(\Modules\Core\Support\FilamentUi::field('discount_amount'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('tax_amount')
                    ->label(\Modules\Core\Support\FilamentUi::field('tax_amount'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('other_costs')
                    ->label(\Modules\Core\Support\FilamentUi::field('other_costs'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total_amount')
                    ->label(\Modules\Core\Support\FilamentUi::field('total_amount'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('amount_paid')
                    ->label(\Modules\Core\Support\FilamentUi::field('amount_paid'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('status')
                    ->label(\Modules\Core\Support\FilamentUi::field('status'))
                    ->searchable(),
                TextColumn::make('payment_status')
                    ->label(\Modules\Core\Support\FilamentUi::field('payment_status'))
                    ->searchable(),
                TextColumn::make('processed_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('processed_at'))
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
                ...ImportTableActions::make(\App\Filament\Imports\VendorBillImporter::class),
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
