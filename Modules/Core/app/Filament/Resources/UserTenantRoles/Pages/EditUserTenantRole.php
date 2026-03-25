<?php

namespace Modules\Core\Filament\Resources\UserTenantRoles\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Core\Filament\Resources\UserTenantRoles\UserTenantRoleResource;

class EditUserTenantRole extends EditRecord
{
    protected static string $resource = UserTenantRoleResource::class;

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
