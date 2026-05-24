<?php

namespace Modules\Event\Filament\Resources\EventCommittees\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Event\Filament\Resources\EventCommittees\EventCommitteeResource;

class EditEventCommittee extends EditRecord
{
    protected static string $resource = EventCommitteeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
