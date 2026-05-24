<?php

namespace Modules\Counseling\Filament\Resources\CaseFollowUps\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Counseling\Filament\Resources\CaseFollowUps\CaseFollowUpResource;

class EditCaseFollowUp extends EditRecord
{
    protected static string $resource = CaseFollowUpResource::class;

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
