<?php

namespace Modules\School\Filament\Exports;

use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Modules\Core\Support\FilamentUi;
use Modules\School\Models\Student;

class StudentExporter extends Exporter
{
    protected static ?string $model = Student::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('nis')->label(FilamentUi::text('NIS')),
            ExportColumn::make('nisn')->label(FilamentUi::text('NISN')),
            ExportColumn::make('user.name')->label(FilamentUi::text('Name')),
            ExportColumn::make('status')->label(FilamentUi::text('Status')),
            ExportColumn::make('entry_date')->label(FilamentUi::text('Entry Date')),
            ExportColumn::make('studentClasses.schoolClass.name')->label(FilamentUi::text('Class')),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your student export has completed and '.number_format($export->successful_rows).' '.str('row')->plural($export->successful_rows).' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.number_format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to export.';
        }

        return $body;
    }
}
