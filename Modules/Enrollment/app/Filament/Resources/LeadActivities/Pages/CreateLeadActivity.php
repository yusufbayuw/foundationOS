<?php

namespace Modules\Enrollment\Filament\Resources\LeadActivities\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Enrollment\Filament\Resources\LeadActivities\LeadActivityResource;

class CreateLeadActivity extends CreateRecord
{
    protected static string $resource = LeadActivityResource::class;
}
