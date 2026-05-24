<?php

namespace Modules\Event\Filament\Resources\EventProposals\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Event\Filament\Resources\EventProposals\EventProposalResource;

class CreateEventProposal extends CreateRecord
{
    protected static string $resource = EventProposalResource::class;
}
