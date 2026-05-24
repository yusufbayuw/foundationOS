<?php

namespace Modules\KpiEnterprise\Filament\Resources\KpiCascades\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\KpiEnterprise\Filament\Resources\KpiCascades\KpiCascadeResource;

class EditKpiCascade extends EditRecord
{
    protected static string $resource = KpiCascadeResource::class;

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
