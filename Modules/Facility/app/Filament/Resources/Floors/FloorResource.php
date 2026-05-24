<?php

namespace Modules\Facility\Filament\Resources\Floors;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Facility\Filament\Resources\Floors\Pages\CreateFloor;
use Modules\Facility\Filament\Resources\Floors\Pages\EditFloor;
use Modules\Facility\Filament\Resources\Floors\Pages\ListFloors;
use Modules\Facility\Filament\Resources\Floors\Pages\ViewFloor;
use Modules\Facility\Filament\Resources\Floors\Schemas\FloorForm;
use Modules\Facility\Filament\Resources\Floors\Schemas\FloorInfolist;
use Modules\Facility\Filament\Resources\Floors\Tables\FloorsTable;
use Modules\Facility\Models\Floor;

class FloorResource extends ModuleResource
{
    protected static ?string $model = Floor::class;

    public static function form(Schema $schema): Schema
    {
        return FloorForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return FloorInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FloorsTable::configure($table);
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
            'index' => ListFloors::route('/'),
            'create' => CreateFloor::route('/create'),
            'view' => ViewFloor::route('/{record}'),
            'edit' => EditFloor::route('/{record}/edit'),
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
