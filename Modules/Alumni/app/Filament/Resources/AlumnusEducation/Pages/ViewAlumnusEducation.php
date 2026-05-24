<?php

namespace Modules\Alumni\Filament\Resources\AlumnusEducation\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Alumni\Filament\Resources\AlumnusEducation\AlumnusEducationResource;

class ViewAlumnusEducation extends ViewRecord
{
    protected static string $resource = AlumnusEducationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
