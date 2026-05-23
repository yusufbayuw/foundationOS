<?php

namespace Modules\Donation\Filament\Resources\Campaigns\Pages;

use Filament\Resources\Pages\ListRecords;
use Modules\Donation\Filament\Resources\Campaigns\CampaignResource;

class ListCampaigns extends ListRecords
{
    protected static string $resource = CampaignResource::class;
}
