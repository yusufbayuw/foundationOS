<?php

namespace Modules\Procurement\Filament\Resources\PurchaseOrders\Tables;

use App\Filament\Imports\PurchaseOrderImporter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ImportTableActions;
use Modules\Core\Support\FilamentUi;

class PurchaseOrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->label(FilamentUi::field('tenant.name'))
                    ->searchable(),
                TextColumn::make('requestForQuotation.id')
                    ->label(FilamentUi::field('requestForQuotation.id'))
                    ->searchable(),
                TextColumn::make('vendor.name')
                    ->label(FilamentUi::field('vendor.name'))
                    ->searchable(),
                TextColumn::make('approved_by')
                    ->label(FilamentUi::field('approved_by'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('po_number')
                    ->label(FilamentUi::field('po_number'))
                    ->searchable(),
                TextColumn::make('po_date')
                    ->label(FilamentUi::field('po_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('delivery_date')
                    ->label(FilamentUi::field('delivery_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('delivery_location')
                    ->label(FilamentUi::field('delivery_location'))
                    ->searchable(),
                TextColumn::make('payment_terms')
                    ->label(FilamentUi::field('payment_terms'))
                    ->searchable(),
                TextColumn::make('subtotal')
                    ->label(FilamentUi::field('subtotal'))
                    ->numeric()
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
                TextColumn::make('shipping_cost')
                    ->label(FilamentUi::field('shipping_cost'))
                    ->money()
                    ->sortable(),
                TextColumn::make('other_costs')
                    ->label(FilamentUi::field('other_costs'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total_amount')
                    ->label(FilamentUi::field('total_amount'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('currency')
                    ->label(FilamentUi::field('currency'))
                    ->searchable(),
                TextColumn::make('exchange_rate')
                    ->label(FilamentUi::field('exchange_rate'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('status')
                    ->label(FilamentUi::field('status'))
                    ->searchable(),
                TextColumn::make('sent_at')
                    ->label(FilamentUi::field('sent_at'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('approved_at')
                    ->label(FilamentUi::field('approved_at'))
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
                SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'sent' => 'Sent',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                        'cancelled' => 'Cancelled',
                        'completed' => 'Completed',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->headerActions([
                ...ImportTableActions::make(PurchaseOrderImporter::class),
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
