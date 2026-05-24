<?php

namespace Modules\Boarding\Filament\Resources\BoardingAttendances;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Boarding\Filament\Resources\BoardingAttendances\Pages\CreateBoardingAttendance;
use Modules\Boarding\Filament\Resources\BoardingAttendances\Pages\EditBoardingAttendance;
use Modules\Boarding\Filament\Resources\BoardingAttendances\Pages\ListBoardingAttendances;
use Modules\Boarding\Filament\Resources\BoardingAttendances\Pages\ViewBoardingAttendance;
use Modules\Boarding\Filament\Resources\BoardingAttendances\Schemas\BoardingAttendanceForm;
use Modules\Boarding\Filament\Resources\BoardingAttendances\Schemas\BoardingAttendanceInfolist;
use Modules\Boarding\Filament\Resources\BoardingAttendances\Tables\BoardingAttendancesTable;
use Modules\Boarding\Models\BoardingAttendance;
use Modules\Core\Filament\Support\ModuleResource;

class BoardingAttendanceResource extends ModuleResource
{
    protected static ?string $model = BoardingAttendance::class;

    public static function form(Schema $schema): Schema
    {
        return BoardingAttendanceForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return BoardingAttendanceInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BoardingAttendancesTable::configure($table);
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
            'index' => ListBoardingAttendances::route('/'),
            'create' => CreateBoardingAttendance::route('/create'),
            'view' => ViewBoardingAttendance::route('/{record}'),
            'edit' => EditBoardingAttendance::route('/{record}/edit'),
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
