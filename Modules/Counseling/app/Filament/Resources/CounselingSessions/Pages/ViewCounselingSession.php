<?php

namespace Modules\Counseling\Filament\Resources\CounselingSessions\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Counseling\Filament\Resources\CounselingSessions\CounselingSessionResource;

class ViewCounselingSession extends ViewRecord
{
    protected static string $resource = CounselingSessionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
