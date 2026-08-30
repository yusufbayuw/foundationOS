<?php

namespace Modules\Marketplace\Filament\Resources\MarketplaceOrders\Tables;

use App\Enums\ShopOrderStatus;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Modules\Core\Support\FilamentUi;
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
                    ->formatStateUsing(fn (ShopOrderStatus $state): string => $state->label())
                    ->badge()
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

                Action::make('markReadyForPickup')
                    ->label(FilamentUi::text('Mark ready for pickup'))
                    ->authorize('update')
                    ->visible(fn ($record): bool => $record->status === ShopOrderStatus::Paid)
                    ->action(function ($record): void {
                        $record->markReadyForPickup();
                    }),
                Action::make('markPickedUp')
                    ->label(FilamentUi::text('Mark picked up'))
                    ->authorize('update')
                    ->visible(fn ($record): bool => $record->status === ShopOrderStatus::ReadyForPickup)
                    ->action(function ($record): void {
                        $record->markPickedUp();
                    }),
                Action::make('reject')
                    ->label(FilamentUi::text('Reject'))
                    ->authorize('update')
                    ->visible(fn ($record): bool => in_array($record->status, [ShopOrderStatus::PendingPayment, ShopOrderStatus::Paid], true))
                    ->schema([
                        Textarea::make('rejection_reason')
                            ->label(FilamentUi::text('Rejection reason'))
                            ->required(),
                    ])
                    ->action(function (array $data, $record): void {
                        $record->reject($data['rejection_reason']);
                    }),
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
