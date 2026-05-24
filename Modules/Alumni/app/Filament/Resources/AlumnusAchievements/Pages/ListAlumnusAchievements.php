<?php

namespace Modules\Alumni\Filament\Resources\AlumnusAchievements\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Alumni\Filament\Resources\AlumnusAchievements\AlumnusAchievementResource;

class ListAlumnusAchievements extends ListRecords
{
    protected static string $resource = AlumnusAchievementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
