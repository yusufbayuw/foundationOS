<?php

namespace Modules\Helpdesk\Filament\Resources\TicketCategories\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Helpdesk\Filament\Resources\TicketCategories\TicketCategoryResource;

class CreateTicketCategory extends CreateRecord
{
    protected static string $resource = TicketCategoryResource::class;
}
