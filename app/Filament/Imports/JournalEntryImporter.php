<?php

namespace App\Filament\Imports;

use Modules\Finance\Models\JournalEntry;

class JournalEntryImporter extends BaseModelImporter
{
    protected static ?string $model = JournalEntry::class;
}
