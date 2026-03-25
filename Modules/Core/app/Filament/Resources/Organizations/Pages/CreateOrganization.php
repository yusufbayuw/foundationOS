<?php

namespace Modules\Core\Filament\Resources\Organizations\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Core\Filament\Resources\Organizations\OrganizationResource;

class CreateOrganization extends CreateRecord
{
    protected static string $resource = OrganizationResource::class;
}
