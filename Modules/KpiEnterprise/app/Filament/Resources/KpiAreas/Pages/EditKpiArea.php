<?php

namespace Modules\KpiEnterprise\Filament\Resources\KpiAreas\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\KpiEnterprise\Filament\Resources\KpiAreas\KpiAreaResource;

class EditKpiArea extends EditRecord
{
    protected static string $resource = KpiAreaResource::class;

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
