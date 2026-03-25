<?php

namespace Modules\Library\Filament\Resources\Members\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Library\Filament\Resources\Members\MemberResource;

class CreateMember extends CreateRecord
{
    protected static string $resource = MemberResource::class;
}
