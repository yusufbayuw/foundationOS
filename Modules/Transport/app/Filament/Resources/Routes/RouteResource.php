<?php

namespace Modules\Transport\Filament\Resources\Routes;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Transport\Filament\Resources\Routes\Pages\CreateRoute;
use Modules\Transport\Filament\Resources\Routes\Pages\EditRoute;
use Modules\Transport\Filament\Resources\Routes\Pages\ListRoutes;
use Modules\Transport\Filament\Resources\Routes\Pages\ViewRoute;
use Modules\Transport\Filament\Resources\Routes\Schemas\RouteForm;
use Modules\Transport\Filament\Resources\Routes\Schemas\RouteInfolist;
use Modules\Transport\Filament\Resources\Routes\Tables\RoutesTable;
use Modules\Transport\Models\Route;

class RouteResource extends ModuleResource
{
    protected static ?string $model = Route::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return RouteForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return RouteInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RoutesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRoutes::route('/'),
            'create' => CreateRoute::route('/create'),
            'view' => ViewRoute::route('/{record}'),
            'edit' => EditRoute::route('/{record}/edit'),
        ];
    }
}
