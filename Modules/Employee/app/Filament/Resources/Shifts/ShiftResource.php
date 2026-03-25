<?php

namespace Modules\Employee\Filament\Resources\Shifts;

use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Employee\Filament\Resources\Shifts\Pages\CreateShift;
use Modules\Employee\Filament\Resources\Shifts\Pages\EditShift;
use Modules\Employee\Filament\Resources\Shifts\Pages\ListShifts;
use Modules\Employee\Filament\Resources\Shifts\Pages\ViewShift;
use Modules\Employee\Filament\Resources\Shifts\Schemas\ShiftForm;
use Modules\Employee\Filament\Resources\Shifts\Schemas\ShiftInfolist;
use Modules\Employee\Filament\Resources\Shifts\Tables\ShiftsTable;
use Modules\Employee\Models\Shift;

class ShiftResource extends LocalizedResource
{
    protected static ?string $model = Shift::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return ShiftForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ShiftInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ShiftsTable::configure($table);
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
            'index' => ListShifts::route('/'),
            'create' => CreateShift::route('/create'),
            'view' => ViewShift::route('/{record}'),
            'edit' => EditShift::route('/{record}/edit'),
        ];
    }
}
