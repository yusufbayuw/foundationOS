<?php

namespace App\Filament\Imports;

use Filament\Actions\Imports\Exceptions\RowImportFailedException;
use Filament\Actions\Imports\ImportColumn;
use Modules\Core\Models\Organization;
use Modules\Finance\Models\JournalEntry;

class JournalEntryImporter extends BaseModelImporter
{
    protected static ?string $model = JournalEntry::class;

    public static function getColumns(): array
    {
        // Import only draft descriptors. Posting, monetary totals and reversal
        // metadata are exclusively maintained by audited finance services.
        return [
            ImportColumn::make('organization_id')->numeric(),
            ImportColumn::make('entry_number'),
            ImportColumn::make('date'),
            ImportColumn::make('description'),
        ];
    }

    protected function beforeSave(): void
    {
        parent::beforeSave();

        $record = $this->getRecord();

        if (! $record instanceof JournalEntry) {
            return;
        }

        if (! Organization::withoutTenantScope()
            ->where('tenant_id', $record->tenant_id)
            ->whereKey($record->organization_id)
            ->exists()) {
            throw new RowImportFailedException('Journal organization must belong to the importing tenant.');
        }

        $record->forceFill([
            'total_debit' => 0,
            'total_credit' => 0,
            'is_balanced' => false,
            'is_posted' => false,
            'posted_at' => null,
            'posted_by' => null,
            'is_reversed' => false,
            'reversed_entry_id' => null,
            'reversal_reason' => null,
        ]);
    }
}
