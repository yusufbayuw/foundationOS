<?php

namespace Modules\School\Filament\Resources\SchoolClasses\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\School\Filament\Resources\SchoolClasses\SchoolClassResource;

class ViewSchoolClass extends ViewRecord
{
    protected static string $resource = SchoolClassResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
