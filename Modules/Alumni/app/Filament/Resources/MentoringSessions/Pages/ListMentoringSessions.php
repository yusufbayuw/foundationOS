<?php

namespace Modules\Alumni\Filament\Resources\MentoringSessions\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Alumni\Filament\Resources\MentoringSessions\MentoringSessionResource;

class ListMentoringSessions extends ListRecords
{
    protected static string $resource = MentoringSessionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
