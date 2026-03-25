<?php

namespace Modules\Finance\Filament\Resources\JournalEntryLines\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Finance\Filament\Resources\JournalEntryLines\JournalEntryLineResource;

class EditJournalEntryLine extends EditRecord
{
    protected static string $resource = JournalEntryLineResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
