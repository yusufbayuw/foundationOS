<?php

namespace Modules\Risk\Filament\Resources\RiskCategories\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Risk\Filament\Resources\RiskCategories\RiskCategoryResource;

class ListRiskCategories extends ListRecords
{
    protected static string $resource = RiskCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
