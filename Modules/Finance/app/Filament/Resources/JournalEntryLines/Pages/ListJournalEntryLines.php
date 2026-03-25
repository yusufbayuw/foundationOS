<?php

namespace Modules\Finance\Filament\Resources\JournalEntryLines\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Finance\Filament\Resources\JournalEntryLines\JournalEntryLineResource;

class ListJournalEntryLines extends ListRecords
{
    protected static string $resource = JournalEntryLineResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
