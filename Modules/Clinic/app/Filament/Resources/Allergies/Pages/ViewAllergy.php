<?php

namespace Modules\Clinic\Filament\Resources\Allergies\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Clinic\Filament\Resources\Allergies\AllergyResource;

class ViewAllergy extends ViewRecord
{
    protected static string $resource = AllergyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
