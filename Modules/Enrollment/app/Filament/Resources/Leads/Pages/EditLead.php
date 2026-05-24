<?php

namespace Modules\Enrollment\Filament\Resources\Leads\Pages;

use Filament\Resources\Pages\EditRecord;
use Modules\Enrollment\Filament\Resources\Leads\LeadResource;

class EditLead extends EditRecord
{
    protected static string $resource = LeadResource::class;
}
