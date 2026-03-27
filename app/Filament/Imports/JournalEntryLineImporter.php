<?php

namespace App\Filament\Imports;

use Modules\Finance\Models\JournalEntryLine;

class JournalEntryLineImporter extends BaseModelImporter
{
    protected static ?string $model = JournalEntryLine::class;
}
