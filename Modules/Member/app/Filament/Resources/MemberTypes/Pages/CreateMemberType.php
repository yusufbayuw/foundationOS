<?php

namespace Modules\Member\Filament\Resources\MemberTypes\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Member\Filament\Resources\MemberTypes\MemberTypeResource;

class CreateMemberType extends CreateRecord
{
    protected static string $resource = MemberTypeResource::class;
}
