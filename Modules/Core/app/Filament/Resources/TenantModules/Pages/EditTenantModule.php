<?php

namespace Modules\Core\Filament\Resources\TenantModules\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Core\Filament\Resources\TenantModules\TenantModuleResource;

class EditTenantModule extends EditRecord
{
    protected static string $resource = TenantModuleResource::class;

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
