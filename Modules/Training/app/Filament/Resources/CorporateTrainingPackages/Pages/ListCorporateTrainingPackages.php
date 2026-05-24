<?php

namespace Modules\Training\Filament\Resources\CorporateTrainingPackages\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Training\Filament\Resources\CorporateTrainingPackages\CorporateTrainingPackageResource;

class ListCorporateTrainingPackages extends ListRecords
{
    protected static string $resource = CorporateTrainingPackageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
