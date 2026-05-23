<?php

namespace Modules\Donation\Filament\Resources\Campaigns\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Donation\Filament\Resources\Campaigns\CampaignResource;

class CreateCampaign extends CreateRecord
{
    protected static string $resource = CampaignResource::class;
}
