<?php

namespace Modules\Property\Filament\Resources\CommercialTenants\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Property\Filament\Resources\CommercialTenants\CommercialTenantResource;

class EditCommercialTenant extends EditRecord
{
    protected static string $resource = CommercialTenantResource::class;

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
