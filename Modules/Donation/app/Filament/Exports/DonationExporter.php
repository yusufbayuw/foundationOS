<?php

namespace Modules\Donation\Filament\Exports;

use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Modules\Core\Support\FilamentUi;
use Modules\Donation\Models\Donation;

class DonationExporter extends Exporter
{
    protected static ?string $model = Donation::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('donor.name')->label(FilamentUi::text('Donor name')),
            ExportColumn::make('donor.email')->label(FilamentUi::text('Donor email')),
            ExportColumn::make('donor.phone')->label(FilamentUi::text('Donor phone')),
            ExportColumn::make('campaign.name')->label(FilamentUi::text('Campaign')),
            ExportColumn::make('amount')->label(FilamentUi::text('Amount')),
            ExportColumn::make('payment_status')->label(FilamentUi::text('Payment status')),
            ExportColumn::make('paid_at')->label(FilamentUi::text('Paid at')),
        ];
    }

    public function getFormats(): array
    {
        return [ExportFormat::Csv];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        return 'Donation export completed with '.number_format($export->successful_rows).' '.str('row')->plural($export->successful_rows).' exported.';
    }
}
