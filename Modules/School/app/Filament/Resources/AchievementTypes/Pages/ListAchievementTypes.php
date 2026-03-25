<?php

namespace Modules\School\Filament\Resources\AchievementTypes\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\School\Filament\Resources\AchievementTypes\AchievementTypeResource;

class ListAchievementTypes extends ListRecords
{
    protected static string $resource = AchievementTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
