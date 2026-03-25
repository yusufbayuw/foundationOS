<?php

namespace Modules\Campus\Filament\Resources\FeederLogs\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Campus\Filament\Resources\FeederLogs\FeederLogResource;

class EditFeederLog extends EditRecord
{
    protected static string $resource = FeederLogResource::class;

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
