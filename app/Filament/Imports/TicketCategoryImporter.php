<?php

namespace App\Filament\Imports;

use Modules\Helpdesk\Models\TicketCategory;

class TicketCategoryImporter extends BaseModelImporter
{
    protected static ?string $model = TicketCategory::class;
}
