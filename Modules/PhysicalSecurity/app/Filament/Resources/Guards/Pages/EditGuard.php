<?php

namespace Modules\PhysicalSecurity\Filament\Resources\Guards\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\PhysicalSecurity\Filament\Resources\Guards\GuardResource;

class EditGuard extends EditRecord
{
    protected static string $resource = GuardResource::class;

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
