<?php

namespace Modules\Legal\Filament\Resources\ContractAttachments\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Legal\Filament\Resources\ContractAttachments\ContractAttachmentResource;

class EditContractAttachment extends EditRecord
{
    protected static string $resource = ContractAttachmentResource::class;

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
