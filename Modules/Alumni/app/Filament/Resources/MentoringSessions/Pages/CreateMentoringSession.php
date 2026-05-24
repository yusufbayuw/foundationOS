<?php

namespace Modules\Alumni\Filament\Resources\MentoringSessions\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Alumni\Filament\Resources\MentoringSessions\MentoringSessionResource;

class CreateMentoringSession extends CreateRecord
{
    protected static string $resource = MentoringSessionResource::class;
}
