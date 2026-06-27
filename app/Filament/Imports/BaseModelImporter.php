<?php

namespace App\Filament\Imports;

use App\Support\TypedValue;
use Filament\Actions\Imports\Exceptions\RowImportFailedException;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Number;

abstract class BaseModelImporter extends Importer
{
    public static function getColumns(): array
    {
        $modelClass = static::getModel();
        /** @var Model $model */
        $model = app($modelClass);

        $casts = $model->getCasts();
        $columns = [];

        foreach ($model->getFillable() as $field) {
            if (in_array($field, ['created_at', 'updated_at', 'deleted_at'], true)) {
                continue;
            }

            if ($field === 'tenant_id' && $model->isRelation('tenant')) {
                continue;
            }

            $column = ImportColumn::make($field);

            $cast = strtolower(TypedValue::string($casts[$field] ?? ''));

            if (in_array($cast, ['bool', 'boolean'], true)) {
                $column->boolean();
            }

            if (
                in_array($cast, ['int', 'integer', 'real', 'float', 'double'], true) ||
                str_starts_with($cast, 'decimal:')
            ) {
                $column->numeric();
            }

            $columns[] = $column;
        }

        return $columns;
    }

    public function resolveRecord(): ?Model
    {
        $modelClass = static::getModel();

        return new $modelClass;
    }

    protected function beforeSave(): void
    {
        $record = $this->getRecord();

        if (! $record) {
            return;
        }

        if (! $record->isRelation('tenant') || ! in_array('tenant_id', $record->getFillable(), true)) {
            return;
        }

        $tenantId = $this->getOptions()['tenant_id'] ?? null;

        if (! is_numeric($tenantId) || (int) $tenantId <= 0) {
            throw new RowImportFailedException('Tenant context is required for tenant-scoped import.');
        }

        $record->setAttribute('tenant_id', (int) $tenantId);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your import has completed and '.Number::format($import->successful_rows).' '.str('row')->plural($import->successful_rows).' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' '.Number::format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to import.';
        }

        return $body;
    }
}
