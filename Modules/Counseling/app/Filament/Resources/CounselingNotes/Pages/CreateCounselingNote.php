<?php

namespace Modules\Counseling\Filament\Resources\CounselingNotes\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Counseling\Filament\Resources\CounselingNotes\CounselingNoteResource;

class CreateCounselingNote extends CreateRecord
{
    protected static string $resource = CounselingNoteResource::class;
}
