<?php

namespace Modules\MerchOrder\Filament\Exports;

use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Modules\Core\Support\FilamentUi;
use Modules\MerchOrder\Models\MerchOrder;

class MerchOrderExporter extends Exporter
{
    protected static ?string $model = MerchOrder::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('code')->label(FilamentUi::text('Order code')),
            ExportColumn::make('customer_name')->label('User/customer'),
            ExportColumn::make('total')->label(FilamentUi::text('Total')),
            ExportColumn::make('status')->label(FilamentUi::text('Status')),
            ExportColumn::make('pickup_status')->label(FilamentUi::text('Pickup status')),
            ExportColumn::make('pickup_date')->label(FilamentUi::text('Pickup date')),
        ];
    }

    public function getFormats(): array
    {
        return [ExportFormat::Csv];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        return 'Merch order export completed with '.number_format($export->successful_rows).' '.str('row')->plural($export->successful_rows).' exported.';
    }
}
