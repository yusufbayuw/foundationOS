<?php

namespace Modules\Boarding\Filament\Resources\RoomInspections;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Boarding\Filament\Resources\RoomInspections\Pages\CreateRoomInspection;
use Modules\Boarding\Filament\Resources\RoomInspections\Pages\EditRoomInspection;
use Modules\Boarding\Filament\Resources\RoomInspections\Pages\ListRoomInspections;
use Modules\Boarding\Filament\Resources\RoomInspections\Pages\ViewRoomInspection;
use Modules\Boarding\Filament\Resources\RoomInspections\Schemas\RoomInspectionForm;
use Modules\Boarding\Filament\Resources\RoomInspections\Schemas\RoomInspectionInfolist;
use Modules\Boarding\Filament\Resources\RoomInspections\Tables\RoomInspectionsTable;
use Modules\Boarding\Models\RoomInspection;
use Modules\Core\Filament\Support\ModuleResource;

class RoomInspectionResource extends ModuleResource
{
    protected static ?string $model = RoomInspection::class;

    public static function form(Schema $schema): Schema
    {
        return RoomInspectionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return RoomInspectionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RoomInspectionsTable::configure($table);
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
            'index' => ListRoomInspections::route('/'),
            'create' => CreateRoomInspection::route('/create'),
            'view' => ViewRoomInspection::route('/{record}'),
            'edit' => EditRoomInspection::route('/{record}/edit'),
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
