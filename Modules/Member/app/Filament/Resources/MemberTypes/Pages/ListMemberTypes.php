<?php

namespace Modules\Member\Filament\Resources\MemberTypes\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Member\Filament\Resources\MemberTypes\MemberTypeResource;

class ListMemberTypes extends ListRecords
{
    protected static string $resource = MemberTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
