<?php

namespace Modules\Counseling\Filament\Resources\CounselingNotes\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Counseling\Filament\Resources\CounselingNotes\CounselingNoteResource;

class ListCounselingNotes extends ListRecords
{
    protected static string $resource = CounselingNoteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
