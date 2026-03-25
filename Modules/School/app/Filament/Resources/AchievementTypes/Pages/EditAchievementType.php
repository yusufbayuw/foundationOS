<?php

namespace Modules\School\Filament\Resources\AchievementTypes\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\School\Filament\Resources\AchievementTypes\AchievementTypeResource;

class EditAchievementType extends EditRecord
{
    protected static string $resource = AchievementTypeResource::class;

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
