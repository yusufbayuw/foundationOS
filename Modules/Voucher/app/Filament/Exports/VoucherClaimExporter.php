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

    public function getFormats(): array
    {
        return [ExportFormat::Csv];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        return 'Voucher claim export completed with '.number_format($export->successful_rows).' '.str('row')->plural($export->successful_rows).' exported.';
    }
}
