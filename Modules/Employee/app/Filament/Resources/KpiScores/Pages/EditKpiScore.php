<?php

namespace Modules\Employee\Filament\Resources\KpiScores\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Employee\Filament\Resources\KpiScores\KpiScoreResource;

class EditKpiScore extends EditRecord
{
    protected static string $resource = KpiScoreResource::class;

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
