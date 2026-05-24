<?php

namespace Modules\Enrollment\Filament\Resources\MarketingCampaigns\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Enrollment\Filament\Resources\MarketingCampaigns\MarketingCampaignResource;

class CreateMarketingCampaign extends CreateRecord
{
    protected static string $resource = MarketingCampaignResource::class;
}
