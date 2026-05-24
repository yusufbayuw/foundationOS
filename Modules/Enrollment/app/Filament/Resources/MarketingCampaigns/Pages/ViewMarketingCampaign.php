<?php

namespace Modules\Enrollment\Filament\Resources\MarketingCampaigns\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Enrollment\Filament\Resources\MarketingCampaigns\MarketingCampaignResource;

class ViewMarketingCampaign extends ViewRecord
{
    protected static string $resource = MarketingCampaignResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
