<?php

namespace Modules\EducationQa\Filament\Resources\ImprovementPlans\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\EducationQa\Filament\Resources\ImprovementPlans\ImprovementPlanResource;

class EditImprovementPlan extends EditRecord
{
    protected static string $resource = ImprovementPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
