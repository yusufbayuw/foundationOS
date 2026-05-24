<?php

namespace Modules\Capacity\Filament\Resources\CapacityForecasts;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Capacity\Filament\Resources\CapacityForecasts\Pages\CreateCapacityForecast;
use Modules\Capacity\Filament\Resources\CapacityForecasts\Pages\EditCapacityForecast;
use Modules\Capacity\Filament\Resources\CapacityForecasts\Pages\ListCapacityForecasts;
use Modules\Capacity\Filament\Resources\CapacityForecasts\Pages\ViewCapacityForecast;
use Modules\Capacity\Filament\Resources\CapacityForecasts\Schemas\CapacityForecastForm;
use Modules\Capacity\Filament\Resources\CapacityForecasts\Schemas\CapacityForecastInfolist;
use Modules\Capacity\Filament\Resources\CapacityForecasts\Tables\CapacityForecastsTable;
use Modules\Capacity\Models\CapacityForecast;
use Modules\Core\Filament\Support\ModuleResource;

class CapacityForecastResource extends ModuleResource
{
    protected static ?string $model = CapacityForecast::class;

    public static function form(Schema $schema): Schema
    {
        return CapacityForecastForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CapacityForecastInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CapacityForecastsTable::configure($table);
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
            'index' => ListCapacityForecasts::route('/'),
            'create' => CreateCapacityForecast::route('/create'),
            'view' => ViewCapacityForecast::route('/{record}'),
            'edit' => EditCapacityForecast::route('/{record}/edit'),
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
