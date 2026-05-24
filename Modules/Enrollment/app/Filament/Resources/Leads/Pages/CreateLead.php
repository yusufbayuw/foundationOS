<?php

namespace Modules\Enrollment\Filament\Resources\Leads\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Enrollment\Filament\Resources\Leads\LeadResource;

class CreateLead extends CreateRecord
{
    protected static string $resource = LeadResource::class;
}
