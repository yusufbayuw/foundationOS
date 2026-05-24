<?php

namespace Modules\Cafeteria\Filament\Resources\MealRatings\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Cafeteria\Filament\Resources\MealRatings\MealRatingResource;

class CreateMealRating extends CreateRecord
{
    protected static string $resource = MealRatingResource::class;
}
