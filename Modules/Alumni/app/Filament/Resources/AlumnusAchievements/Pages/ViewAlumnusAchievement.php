<?php

namespace Modules\Alumni\Filament\Resources\AlumnusAchievements\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Alumni\Filament\Resources\AlumnusAchievements\AlumnusAchievementResource;

class ViewAlumnusAchievement extends ViewRecord
{
    protected static string $resource = AlumnusAchievementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
