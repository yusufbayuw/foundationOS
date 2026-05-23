<?php

namespace Modules\EducationQa\Filament\Resources\QualityStandards\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\EducationQa\Filament\Resources\QualityStandards\QualityStandardResource;

class CreateQualityStandard extends CreateRecord
{
    protected static string $resource = QualityStandardResource::class;
}
