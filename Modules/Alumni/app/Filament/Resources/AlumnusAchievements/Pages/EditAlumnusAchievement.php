<?php

namespace Modules\Alumni\Filament\Resources\AlumnusAchievements\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Alumni\Filament\Resources\AlumnusAchievements\AlumnusAchievementResource;

class EditAlumnusAchievement extends EditRecord
{
    protected static string $resource = AlumnusAchievementResource::class;

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
