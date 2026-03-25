<?php

namespace Modules\Employee\Filament\Resources\EmploymentContracts\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Employee\Filament\Resources\EmploymentContracts\EmploymentContractResource;

class EditEmploymentContract extends EditRecord
{
    protected static string $resource = EmploymentContractResource::class;

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
