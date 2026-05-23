<?php

namespace Modules\Enrollment\Filament\Resources\Leads\Pages;

use Filament\Resources\Pages\ListRecords;
use Modules\Enrollment\Filament\Resources\Leads\LeadResource;

class ListLeads extends ListRecords
{
    protected static string $resource = LeadResource::class;
}
