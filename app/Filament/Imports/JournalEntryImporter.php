<?php

namespace App\Filament\Imports;

use Filament\Actions\Imports\ImportColumn;
use Modules\Finance\Models\JournalEntry;

class JournalEntryImporter extends BaseModelImporter
{
    protected static ?string $model = JournalEntry::class;

    public static function getColumns(): array
    {
        return array_values(array_filter(
            parent::getColumns(),
            static fn (ImportColumn $column): bool => ! in_array($column->getName(), [
                'is_balanced', 'is_posted', 'posted_at', 'posted_by', 'is_reversed', 'reversal_reason',
            ], true),
        ));
    }

    protected function beforeSave(): void
    {
        parent::beforeSave();

        $this->getRecord()?->forceFill([
            'is_balanced' => false,
            'is_posted' => false,
            'is_reversed' => false,
        ]);
    }
}
