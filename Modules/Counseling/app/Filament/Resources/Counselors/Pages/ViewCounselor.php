<?php

namespace Modules\Counseling\Filament\Resources\Counselors\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Counseling\Filament\Resources\Counselors\CounselorResource;

class ViewCounselor extends ViewRecord
{
    protected static string $resource = CounselorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
