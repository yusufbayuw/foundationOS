<?php

namespace Modules\PhysicalSecurity\Filament\Resources\Visitors\Pages;

use Filament\Resources\Pages\ListRecords;
use Modules\PhysicalSecurity\Filament\Resources\Visitors\VisitorResource;

class ListVisitors extends ListRecords
{
    protected static string $resource = VisitorResource::class;
}
