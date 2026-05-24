<?php

namespace Modules\Risk\Filament\Resources\RiskCategories\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Risk\Filament\Resources\RiskCategories\RiskCategoryResource;

class CreateRiskCategory extends CreateRecord
{
    protected static string $resource = RiskCategoryResource::class;
}
