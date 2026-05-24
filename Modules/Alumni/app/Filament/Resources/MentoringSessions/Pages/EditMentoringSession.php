<?php

namespace Modules\Alumni\Filament\Resources\MentoringSessions\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Alumni\Filament\Resources\MentoringSessions\MentoringSessionResource;

class EditMentoringSession extends EditRecord
{
    protected static string $resource = MentoringSessionResource::class;

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
