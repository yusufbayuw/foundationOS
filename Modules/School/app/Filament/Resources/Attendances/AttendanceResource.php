<?php

namespace Modules\School\Filament\Resources\Attendances;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Modules\School\Filament\Resources\Attendances\Pages\CreateAttendance;
use Modules\School\Filament\Resources\Attendances\Pages\EditAttendance;
use Modules\School\Filament\Resources\Attendances\Pages\ListAttendances;
use Modules\School\Filament\Resources\Attendances\Pages\ViewAttendance;
use Modules\School\Filament\Resources\Attendances\Schemas\AttendanceForm;
use Modules\School\Filament\Resources\Attendances\Schemas\AttendanceInfolist;
use Modules\School\Filament\Resources\Attendances\Tables\AttendancesTable;
use Modules\School\Models\Attendance;

class AttendanceResource extends LocalizedResource
{
    protected static ?string $model = Attendance::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return AttendanceForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AttendanceInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AttendancesTable::configure($table);
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
            'index' => ListAttendances::route('/'),
            'create' => CreateAttendance::route('/create'),
            'view' => ViewAttendance::route('/{record}'),
            'edit' => EditAttendance::route('/{record}/edit'),
        ];
    }
}
