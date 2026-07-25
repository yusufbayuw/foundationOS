<?php

namespace Modules\Sales\Filament\Exports;

use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Modules\Core\Support\FilamentUi;
use Modules\Sales\Models\VoucherClaim;

class VoucherClaimExporter extends Exporter
{
    protected static ?string $model = VoucherClaim::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('voucher.title')->label(FilamentUi::text('Voucher Title')),
            ExportColumn::make('voucher.code')->label(FilamentUi::text('Voucher Code')),
            ExportColumn::make('user.name')->label(FilamentUi::text('User Name')),
            ExportColumn::make('user.email')->label(FilamentUi::text('User Email')),
            ExportColumn::make('claim_code')->label(FilamentUi::field('claim_code')),
            ExportColumn::make('status')->label(FilamentUi::field('status')),
            ExportColumn::make('claimed_at')->label(FilamentUi::field('claimed_at')),
            ExportColumn::make('used_at')->label(FilamentUi::field('used_at')),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your voucher claim export has completed and '.number_format($export->successful_rows).' '.str('row')->plural($export->successful_rows).' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.number_format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to export.';
        }

        return $body;
    }
}
