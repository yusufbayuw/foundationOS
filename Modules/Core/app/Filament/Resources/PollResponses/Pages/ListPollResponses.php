<?php

namespace Modules\Core\Filament\Resources\PollResponses\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Core\Filament\Resources\PollResponses\PollResponseResource;

class ListPollResponses extends ListRecords
{
    protected static string $resource = PollResponseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
