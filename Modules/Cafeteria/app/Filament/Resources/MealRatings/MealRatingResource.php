<?php

namespace Modules\Cafeteria\Filament\Resources\MealRatings;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Cafeteria\Filament\Resources\MealRatings\Pages\CreateMealRating;
use Modules\Cafeteria\Filament\Resources\MealRatings\Pages\EditMealRating;
use Modules\Cafeteria\Filament\Resources\MealRatings\Pages\ListMealRatings;
use Modules\Cafeteria\Filament\Resources\MealRatings\Pages\ViewMealRating;
use Modules\Cafeteria\Filament\Resources\MealRatings\Schemas\MealRatingForm;
use Modules\Cafeteria\Filament\Resources\MealRatings\Schemas\MealRatingInfolist;
use Modules\Cafeteria\Filament\Resources\MealRatings\Tables\MealRatingsTable;
use Modules\Cafeteria\Models\MealRating;
use Modules\Core\Filament\Support\ModuleResource;

class MealRatingResource extends ModuleResource
{
    protected static ?string $model = MealRating::class;

    public static function form(Schema $schema): Schema
    {
        return MealRatingForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return MealRatingInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MealRatingsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMealRatings::route('/'),
            'create' => CreateMealRating::route('/create'),
            'view' => ViewMealRating::route('/{record}'),
            'edit' => EditMealRating::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
