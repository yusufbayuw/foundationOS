<?php

namespace Modules\Marketplace\Filament\Exports;

use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Modules\Marketplace\Models\MarketplaceOrder;

class MarketplaceOrderExporter extends Exporter
{
    protected static ?string $model = MarketplaceOrder::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('code')->label('Order code'),
            ExportColumn::make('customer_name')->label('User/customer'),
            ExportColumn::make('total')->label('Total'),
            ExportColumn::make('status')->label('Status'),
            ExportColumn::make('pickup_status')->label('Pickup status'),
            ExportColumn::make('pickup_date')->label('Pickup date'),
        ];
    }

    public function getFormats(): array
    {
        return [ExportFormat::Csv];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        return 'Marketplace order export completed with '.number_format($export->successful_rows).' '.str('row')->plural($export->successful_rows).' exported.';
    }
}
