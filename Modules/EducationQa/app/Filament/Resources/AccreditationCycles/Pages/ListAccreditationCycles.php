<?php

namespace Modules\EducationQa\Filament\Resources\AccreditationCycles\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\EducationQa\Filament\Resources\AccreditationCycles\AccreditationCycleResource;

class ListAccreditationCycles extends ListRecords
{
    protected static string $resource = AccreditationCycleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
