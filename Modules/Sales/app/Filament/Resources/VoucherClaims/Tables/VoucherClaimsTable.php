<?php

namespace Modules\Sales\Filament\Resources\VoucherClaims\Tables;

use Filament\Actions\ExportAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Modules\Core\Support\FilamentUi;
use Modules\Sales\Filament\Exports\VoucherClaimExporter;

class VoucherClaimsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('voucher.title')
                    ->label(FilamentUi::text('Voucher Title'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('voucher.code')
                    ->label(FilamentUi::text('Voucher Code'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label(FilamentUi::text('User Name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.email')
                    ->label(FilamentUi::text('User Email'))
                    ->searchable(),
                TextColumn::make('claim_code')
                    ->label(FilamentUi::field('claim_code'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->label(FilamentUi::field('status'))
                    ->badge()
                    ->sortable(),
                TextColumn::make('claimed_at')
                    ->label(FilamentUi::field('claimed_at'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('used_at')
                    ->label(FilamentUi::field('used_at'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'claimed' => 'Claimed',
                        'used' => 'Used',
                        'expired' => 'Expired',
                        'cancelled' => 'Cancelled',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->headerActions([
                ExportAction::make()
                    ->exporter(VoucherClaimExporter::class),
            ]);
    }
}
