<?php

namespace Modules\MerchOrder\Filament\Resources\MerchOrders\Tables;

use App\Enums\ShopOrderStatus;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Modules\Core\Support\FilamentUi;

class MerchOrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')->label(FilamentUi::field('code'))->searchable(),
                TextColumn::make('name')->label(FilamentUi::field('name'))->searchable()->sortable(),
                TextColumn::make('status')->label(FilamentUi::field('status'))->formatStateUsing(fn (ShopOrderStatus $state): string => $state->label())->badge(),
            ])
            ->recordActions([

                Action::make('markReadyForPickup')
                    ->label('Mark ready for pickup')
                    ->visible(fn ($record): bool => $record->status === ShopOrderStatus::Paid)
                    ->action(function ($record): void {
                        $record->markReadyForPickup();
                    }),
                Action::make('markPickedUp')
                    ->label('Mark picked up')
                    ->visible(fn ($record): bool => $record->status === ShopOrderStatus::ReadyForPickup)
                    ->action(function ($record): void {
                        $record->markPickedUp();
                    }),
                Action::make('reject')
                    ->label('Reject')
                    ->visible(fn ($record): bool => in_array($record->status, [ShopOrderStatus::PendingPayment, ShopOrderStatus::Paid], true))
                    ->schema([
                        Textarea::make('rejection_reason')
                            ->label('Rejection reason')
                            ->required(),
                    ])
                    ->action(function (array $data, $record): void {
                        $record->reject($data['rejection_reason']);
                    }),
            ])
            ->defaultSort('name');
    }
}
