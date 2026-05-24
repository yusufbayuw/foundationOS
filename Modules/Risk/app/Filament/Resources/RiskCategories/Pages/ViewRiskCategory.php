<?php

namespace Modules\Risk\Filament\Resources\RiskCategories\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Risk\Filament\Resources\RiskCategories\RiskCategoryResource;

class ViewRiskCategory extends ViewRecord
{
    protected static string $resource = RiskCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
