<?php

namespace Modules\Clinic\Filament\Resources\MedicalHistories\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Clinic\Filament\Resources\MedicalHistories\MedicalHistoryResource;

class ListMedicalHistories extends ListRecords
{
    protected static string $resource = MedicalHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
