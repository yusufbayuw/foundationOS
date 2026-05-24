<?php

namespace Modules\Counseling\Filament\Resources\CounselingNotes\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Counseling\Filament\Resources\CounselingNotes\CounselingNoteResource;

class EditCounselingNote extends EditRecord
{
    protected static string $resource = CounselingNoteResource::class;

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
