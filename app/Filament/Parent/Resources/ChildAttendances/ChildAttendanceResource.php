<?php

namespace App\Filament\Parent\Resources\ChildAttendances;

use App\Filament\Parent\Resources\ChildAttendances\Pages\ListChildAttendances;
use App\Filament\Parent\Resources\ChildAttendances\Tables\ChildAttendancesTable;
use App\Filament\Parent\Support\Concerns\ScopesToParentChildren;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Modules\Core\Support\FilamentUi;
use Modules\School\Models\Attendance;

class ChildAttendanceResource extends Resource
{
    use ScopesToParentChildren;

    protected static ?string $model = Attendance::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::CalendarDays;

    protected static ?string $slug = 'child-attendance';

    public static function getNavigationLabel(): string
    {
        return FilamentUi::text('Attendance');
    }

    public static function table(Table $table): Table
    {
        return ChildAttendancesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListChildAttendances::route('/'),
        ];
    }
}
