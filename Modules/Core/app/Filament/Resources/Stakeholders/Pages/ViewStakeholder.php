<?php

namespace Modules\Core\Filament\Resources\Stakeholders\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Core\Filament\Resources\Stakeholders\StakeholderResource;

class ViewStakeholder extends ViewRecord
{
    protected static string $resource = StakeholderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
