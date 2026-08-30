<?php

namespace Modules\Member\Filament\Resources\MemberTypes\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Member\Filament\Resources\MemberTypes\MemberTypeResource;

class EditMemberType extends EditRecord
{
    protected static string $resource = MemberTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
