<?php

namespace Modules\Finance\Filament\Resources\JournalEntryLines\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Finance\Filament\Resources\JournalEntryLines\JournalEntryLineResource;

class ViewJournalEntryLine extends ViewRecord
{
    protected static string $resource = JournalEntryLineResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
