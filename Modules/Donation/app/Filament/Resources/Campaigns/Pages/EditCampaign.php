<?php

namespace Modules\Donation\Filament\Resources\Campaigns\Pages;

use Filament\Resources\Pages\EditRecord;
use Modules\Donation\Filament\Resources\Campaigns\CampaignResource;

class EditCampaign extends EditRecord
{
    protected static string $resource = CampaignResource::class;
}
