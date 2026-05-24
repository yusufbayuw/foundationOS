<?php

namespace Modules\Alumni\Filament\Resources\MentoringSessions\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Alumni\Filament\Resources\MentoringSessions\MentoringSessionResource;

class ViewMentoringSession extends ViewRecord
{
    protected static string $resource = MentoringSessionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
