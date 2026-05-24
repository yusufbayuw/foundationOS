<?php

namespace Modules\Core\Filament\Resources\PollResponses\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Core\Filament\Resources\PollResponses\PollResponseResource;

class ViewPollResponse extends ViewRecord
{
    protected static string $resource = PollResponseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
