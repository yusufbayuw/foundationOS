<?php

namespace Modules\Enrollment\Filament\Resources\LeadSources\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Enrollment\Filament\Resources\LeadSources\LeadSourceResource;

class CreateLeadSource extends CreateRecord
{
    protected static string $resource = LeadSourceResource::class;
}
