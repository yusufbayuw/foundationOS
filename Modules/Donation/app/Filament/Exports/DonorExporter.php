<?php

namespace Modules\Donation\Filament\Exports;

use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Modules\Donation\Models\Donor;

class DonorExporter extends Exporter
{
    protected static ?string $model = Donor::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('name')->label('Donor name'),
            ExportColumn::make('email')->label('Donor email'),
            ExportColumn::make('phone')->label('Donor phone'),
        ];
    }

    public function getFormats(): array
    {
        return [ExportFormat::Csv];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        return 'Donor export completed with '.number_format($export->successful_rows).' '.str('row')->plural($export->successful_rows).' exported.';
    }
}
