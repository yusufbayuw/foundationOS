<?php

namespace Modules\Counseling\Filament\Resources\CounselingNotes\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Counseling\Filament\Resources\CounselingNotes\CounselingNoteResource;

class ViewCounselingNote extends ViewRecord
{
    protected static string $resource = CounselingNoteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
