<?php

namespace Modules\Member\Filament\Resources\MemberTypes\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Member\Filament\Resources\MemberTypes\MemberTypeResource;

class ViewMemberType extends ViewRecord
{
    protected static string $resource = MemberTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
