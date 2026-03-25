<?php

namespace Modules\Finance\Filament\Resources\JournalEntries\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Finance\Filament\Resources\JournalEntries\JournalEntryResource;

class CreateJournalEntry extends CreateRecord
{
    protected static string $resource = JournalEntryResource::class;
}
