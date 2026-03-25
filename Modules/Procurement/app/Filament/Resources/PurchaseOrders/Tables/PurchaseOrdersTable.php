<?php

namespace Modules\Procurement\Filament\Resources\PurchaseOrders\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PurchaseOrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('tenant.name'))
                    ->searchable(),
                TextColumn::make('requestForQuotation.id')
                    ->label(\Modules\Core\Support\FilamentUi::field('requestForQuotation.id'))
                    ->searchable(),
                TextColumn::make('vendor.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('vendor.name'))
                    ->searchable(),
                TextColumn::make('approved_by')
                    ->label(\Modules\Core\Support\FilamentUi::field('approved_by'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('po_number')
                    ->label(\Modules\Core\Support\FilamentUi::field('po_number'))
                    ->searchable(),
                TextColumn::make('po_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('po_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('delivery_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('delivery_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('delivery_location')
                    ->label(\Modules\Core\Support\FilamentUi::field('delivery_location'))
                    ->searchable(),
                TextColumn::make('payment_terms')
                    ->label(\Modules\Core\Support\FilamentUi::field('payment_terms'))
                    ->searchable(),
                TextColumn::make('subtotal')
                    ->label(\Modules\Core\Support\FilamentUi::field('subtotal'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('discount_amount')
                    ->label(\Modules\Core\Support\FilamentUi::field('discount_amount'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('tax_percentage')
                    ->label(\Modules\Core\Support\FilamentUi::field('tax_percentage'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('tax_amount')
                    ->label(\Modules\Core\Support\FilamentUi::field('tax_amount'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('shipping_cost')
                    ->label(\Modules\Core\Support\FilamentUi::field('shipping_cost'))
                    ->money()
                    ->sortable(),
                TextColumn::make('other_costs')
                    ->label(\Modules\Core\Support\FilamentUi::field('other_costs'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total_amount')
                    ->label(\Modules\Core\Support\FilamentUi::field('total_amount'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('currency')
                    ->label(\Modules\Core\Support\FilamentUi::field('currency'))
                    ->searchable(),
                TextColumn::make('exchange_rate')
                    ->label(\Modules\Core\Support\FilamentUi::field('exchange_rate'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('status')
                    ->label(\Modules\Core\Support\FilamentUi::field('status'))
                    ->searchable(),
                TextColumn::make('sent_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('sent_at'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('approved_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('approved_at'))
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
