<?php

namespace Modules\School\Filament\Resources\StudentAchievements\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\School\Filament\Resources\StudentAchievements\StudentAchievementResource;

class CreateStudentAchievement extends CreateRecord
{
    protected static string $resource = StudentAchievementResource::class;
}
