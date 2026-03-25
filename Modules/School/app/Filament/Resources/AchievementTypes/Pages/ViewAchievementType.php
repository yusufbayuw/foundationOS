<?php

namespace Modules\School\Filament\Resources\AchievementTypes\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\School\Filament\Resources\AchievementTypes\AchievementTypeResource;

class ViewAchievementType extends ViewRecord
{
    protected static string $resource = AchievementTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
