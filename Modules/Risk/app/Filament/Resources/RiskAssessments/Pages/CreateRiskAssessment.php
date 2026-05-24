<?php

namespace Modules\Risk\Filament\Resources\RiskAssessments\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Risk\Filament\Resources\RiskAssessments\RiskAssessmentResource;

class CreateRiskAssessment extends CreateRecord
{
    protected static string $resource = RiskAssessmentResource::class;
}
