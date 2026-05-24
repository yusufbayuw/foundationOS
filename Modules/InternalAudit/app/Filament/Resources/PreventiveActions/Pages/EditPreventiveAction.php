<?php

namespace Modules\InternalAudit\Filament\Resources\PreventiveActions\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\InternalAudit\Filament\Resources\PreventiveActions\PreventiveActionResource;

class EditPreventiveAction extends EditRecord
{
    protected static string $resource = PreventiveActionResource::class;

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
