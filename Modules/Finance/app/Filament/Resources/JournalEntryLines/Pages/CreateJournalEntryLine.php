<?php

namespace Modules\Finance\Filament\Resources\JournalEntryLines\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Finance\Filament\Resources\JournalEntryLines\JournalEntryLineResource;

class CreateJournalEntryLine extends CreateRecord
{
    protected static string $resource = JournalEntryLineResource::class;
}
