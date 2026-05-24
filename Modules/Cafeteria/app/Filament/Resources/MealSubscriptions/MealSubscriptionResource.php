<?php

namespace Modules\Cafeteria\Filament\Resources\MealSubscriptions;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Cafeteria\Filament\Resources\MealSubscriptions\Pages\CreateMealSubscription;
use Modules\Cafeteria\Filament\Resources\MealSubscriptions\Pages\EditMealSubscription;
use Modules\Cafeteria\Filament\Resources\MealSubscriptions\Pages\ListMealSubscriptions;
use Modules\Cafeteria\Filament\Resources\MealSubscriptions\Pages\ViewMealSubscription;
use Modules\Cafeteria\Filament\Resources\MealSubscriptions\Schemas\MealSubscriptionForm;
use Modules\Cafeteria\Filament\Resources\MealSubscriptions\Schemas\MealSubscriptionInfolist;
use Modules\Cafeteria\Filament\Resources\MealSubscriptions\Tables\MealSubscriptionsTable;
use Modules\Cafeteria\Models\MealSubscription;
use Modules\Core\Filament\Support\ModuleResource;

class MealSubscriptionResource extends ModuleResource
{
    protected static ?string $model = MealSubscription::class;

    public static function form(Schema $schema): Schema
    {
        return MealSubscriptionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return MealSubscriptionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MealSubscriptionsTable::configure($table);
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
            'index' => ListMealSubscriptions::route('/'),
            'create' => CreateMealSubscription::route('/create'),
            'view' => ViewMealSubscription::route('/{record}'),
            'edit' => EditMealSubscription::route('/{record}/edit'),
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
