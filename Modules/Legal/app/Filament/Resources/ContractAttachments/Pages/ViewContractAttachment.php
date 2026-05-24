<?php

namespace Modules\Legal\Filament\Resources\ContractAttachments\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Legal\Filament\Resources\ContractAttachments\ContractAttachmentResource;

class ViewContractAttachment extends ViewRecord
{
    protected static string $resource = ContractAttachmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
