<?php

namespace Modules\EducationQa\Filament\Resources\AccreditationCycles\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\EducationQa\Filament\Resources\AccreditationCycles\AccreditationCycleResource;

class ViewAccreditationCycle extends ViewRecord
{
    protected static string $resource = AccreditationCycleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
