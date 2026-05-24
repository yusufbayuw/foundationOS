<?php

namespace Modules\Alumni\Filament\Resources\CompanyPartners\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Alumni\Filament\Resources\CompanyPartners\CompanyPartnerResource;

class ListCompanyPartners extends ListRecords
{
    protected static string $resource = CompanyPartnerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
