<?php

namespace Modules\Voucher\Filament\Exports;

use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Modules\Core\Support\FilamentUi;
use Modules\Voucher\Models\VoucherClaim;

class VoucherClaimExporter extends Exporter
{
    protected static ?string $model = VoucherClaim::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('voucher.name')->label(FilamentUi::text('Voucher')),
            ExportColumn::make('user.name')->label(FilamentUi::text('User')),
            ExportColumn::make('claim_code')->label(FilamentUi::text('Claim code')),
            ExportColumn::make('status')->label(FilamentUi::text('Status')),
            ExportColumn::make('claimed_at')->label(FilamentUi::text('Claimed at')),
            ExportColumn::make('used_at')->label(FilamentUi::text('Used at')),
        ];
    }

    public function getFormats(): array
    {
        return [ExportFormat::Csv];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        return 'Voucher claim export completed with '.number_format($export->successful_rows).' '.str('row')->plural($export->successful_rows).' exported.';
    }
}
