<?php

namespace Modules\Voucher\Filament\Resources\VoucherClaims\Tables;

use Filament\Actions\ExportAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Modules\Voucher\Filament\Exports\VoucherClaimExporter;
use Modules\Voucher\Models\VoucherClaim;

class VoucherClaimsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('voucher.name')->searchable(),
                TextColumn::make('user.name')->searchable(),
                TextColumn::make('claim_code')->searchable(),
                TextColumn::make('status')->badge(),
                TextColumn::make('claimed_at')->dateTime(),
                TextColumn::make('used_at')->dateTime(),
            ])
            ->headerActions([
                ExportAction::make()
                    ->exporter(VoucherClaimExporter::class)
                    ->visible(fn (): bool => auth()->user()?->can('export', VoucherClaim::class) ?? false)
                    ->authorize(fn (): bool => auth()->user()?->can('export', VoucherClaim::class) ?? false),
            ])
            ->recordActions([
                ViewAction::make(),
            ]);
    }
}
