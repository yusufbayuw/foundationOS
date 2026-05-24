<?php

namespace Modules\Boarding\Filament\Resources\RoomAssignments;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Boarding\Filament\Resources\RoomAssignments\Pages\CreateRoomAssignment;
use Modules\Boarding\Filament\Resources\RoomAssignments\Pages\EditRoomAssignment;
use Modules\Boarding\Filament\Resources\RoomAssignments\Pages\ListRoomAssignments;
use Modules\Boarding\Filament\Resources\RoomAssignments\Pages\ViewRoomAssignment;
use Modules\Boarding\Filament\Resources\RoomAssignments\Schemas\RoomAssignmentForm;
use Modules\Boarding\Filament\Resources\RoomAssignments\Schemas\RoomAssignmentInfolist;
use Modules\Boarding\Filament\Resources\RoomAssignments\Tables\RoomAssignmentsTable;
use Modules\Boarding\Models\RoomAssignment;
use Modules\Core\Filament\Support\ModuleResource;

class RoomAssignmentResource extends ModuleResource
{
    protected static ?string $model = RoomAssignment::class;

    public static function form(Schema $schema): Schema
    {
        return RoomAssignmentForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return RoomAssignmentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RoomAssignmentsTable::configure($table);
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
            'index' => ListRoomAssignments::route('/'),
            'create' => CreateRoomAssignment::route('/create'),
            'view' => ViewRoomAssignment::route('/{record}'),
            'edit' => EditRoomAssignment::route('/{record}/edit'),
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
