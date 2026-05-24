<?php

namespace Modules\Legal\Filament\Resources\ContractAttachments\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Legal\Filament\Resources\ContractAttachments\ContractAttachmentResource;

class ListContractAttachments extends ListRecords
{
    protected static string $resource = ContractAttachmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
