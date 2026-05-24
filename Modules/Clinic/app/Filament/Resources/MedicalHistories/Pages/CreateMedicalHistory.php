<?php

namespace Modules\Clinic\Filament\Resources\MedicalHistories\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Clinic\Filament\Resources\MedicalHistories\MedicalHistoryResource;

class CreateMedicalHistory extends CreateRecord
{
    protected static string $resource = MedicalHistoryResource::class;
}
