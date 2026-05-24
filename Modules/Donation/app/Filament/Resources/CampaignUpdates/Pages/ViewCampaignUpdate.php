<?php

namespace Modules\Donation\Filament\Resources\CampaignUpdates\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Donation\Filament\Resources\CampaignUpdates\CampaignUpdateResource;

class ViewCampaignUpdate extends ViewRecord
{
    protected static string $resource = CampaignUpdateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
