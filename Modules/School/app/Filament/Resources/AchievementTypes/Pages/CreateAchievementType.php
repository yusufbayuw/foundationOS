<?php

namespace Modules\School\Filament\Resources\AchievementTypes\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\School\Filament\Resources\AchievementTypes\AchievementTypeResource;

class CreateAchievementType extends CreateRecord
{
    protected static string $resource = AchievementTypeResource::class;
}
