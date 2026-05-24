<?php

namespace Modules\Training\Filament\Resources\CorporateTrainingPackages\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Training\Filament\Resources\CorporateTrainingPackages\CorporateTrainingPackageResource;

class ViewCorporateTrainingPackage extends ViewRecord
{
    protected static string $resource = CorporateTrainingPackageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
