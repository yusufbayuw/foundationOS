<?php

namespace Modules\Alumni\Filament\Resources\CompanyPartners\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Alumni\Filament\Resources\CompanyPartners\CompanyPartnerResource;

class ViewCompanyPartner extends ViewRecord
{
    protected static string $resource = CompanyPartnerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
