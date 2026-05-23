<?php

namespace Modules\PhysicalSecurity\Filament\Resources\Visitors\Pages;

use Filament\Resources\Pages\EditRecord;
use Modules\PhysicalSecurity\Filament\Resources\Visitors\VisitorResource;

class EditVisitor extends EditRecord
{
    protected static string $resource = VisitorResource::class;
}
