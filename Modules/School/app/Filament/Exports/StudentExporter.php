<?php

namespace Modules\School\Filament\Exports;

use Modules\School\Models\Student;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class StudentExporter extends Exporter
{
    protected static ?string $model = Student::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('nis')->label('NIS'),
            ExportColumn::make('nisn')->label('NISN'),
            ExportColumn::make('user.name')->label('Name'),
            ExportColumn::make('status')->label('Status'),
            ExportColumn::make('entry_date')->label('Entry Date'),
            ExportColumn::make('studentClasses.schoolClass.name')->label('Class'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your student export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
