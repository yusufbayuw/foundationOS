<?php

namespace Modules\School\Filament\Resources\Students\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;
use Modules\School\Filament\Resources\Students\Schemas\StudentProfile360Infolist;
use Modules\School\Filament\Resources\Students\StudentResource;

class ViewStudent extends ViewRecord
{
    protected static string $resource = StudentResource::class;

    public function infolist(Schema $schema): Schema
    {
        return StudentProfile360Infolist::configure($schema);
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
