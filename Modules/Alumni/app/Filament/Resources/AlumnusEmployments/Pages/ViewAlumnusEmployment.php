<?php

namespace Modules\Alumni\Filament\Resources\AlumnusEmployments\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Alumni\Filament\Resources\AlumnusEmployments\AlumnusEmploymentResource;

class ViewAlumnusEmployment extends ViewRecord
{
    protected static string $resource = AlumnusEmploymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
