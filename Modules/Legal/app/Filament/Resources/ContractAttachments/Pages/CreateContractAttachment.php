<?php

namespace Modules\Legal\Filament\Resources\ContractAttachments\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Legal\Filament\Resources\ContractAttachments\ContractAttachmentResource;

class CreateContractAttachment extends CreateRecord
{
    protected static string $resource = ContractAttachmentResource::class;
}
