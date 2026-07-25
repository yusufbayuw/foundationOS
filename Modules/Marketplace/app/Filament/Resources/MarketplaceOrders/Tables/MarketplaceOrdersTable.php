<?php

namespace Modules\Marketplace\Filament\Resources\MarketplaceOrders\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Modules\Marketplace\Filament\Exports\MarketplaceOrderExporter;
use Modules\Marketplace\Models\MarketplaceOrder;

class MarketplaceOrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->searchable(),
                TextColumn::make('organization_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('code')
                    ->searchable(),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('status')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('seller.name')
                    ->searchable(),
            ])
            ->filters([
                //
            ])

            ->headerActions([
                ExportAction::make()
                    ->exporter(MarketplaceOrderExporter::class)
                    ->visible(fn (): bool => auth()->user()?->can('export', MarketplaceOrder::class) ?? false)
                    ->authorize(fn (): bool => auth()->user()?->can('export', MarketplaceOrder::class) ?? false),
            ])
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
