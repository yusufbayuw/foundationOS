<?php

namespace Modules\Donation\Filament\Resources\CampaignUpdates\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Donation\Filament\Resources\CampaignUpdates\CampaignUpdateResource;

class ListCampaignUpdates extends ListRecords
{
    protected static string $resource = CampaignUpdateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
