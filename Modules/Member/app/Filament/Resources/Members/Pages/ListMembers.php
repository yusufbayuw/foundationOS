<?php

namespace Modules\Member\Filament\Resources\Members\Pages;

use Filament\Resources\Pages\ListRecords;
use Modules\Member\Filament\Resources\Members\MemberResource;

class ListMembers extends ListRecords
{
    protected static string $resource = MemberResource::class;
}
