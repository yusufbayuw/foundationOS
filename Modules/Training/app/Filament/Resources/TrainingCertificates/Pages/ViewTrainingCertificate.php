<?php

namespace Modules\Training\Filament\Resources\TrainingCertificates\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Training\Filament\Resources\TrainingCertificates\TrainingCertificateResource;

class ViewTrainingCertificate extends ViewRecord
{
    protected static string $resource = TrainingCertificateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
