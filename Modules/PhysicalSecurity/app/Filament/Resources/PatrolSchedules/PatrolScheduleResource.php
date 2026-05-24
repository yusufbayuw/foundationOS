<?php

namespace Modules\PhysicalSecurity\Filament\Resources\PatrolSchedules;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\PhysicalSecurity\Filament\Resources\PatrolSchedules\Pages\CreatePatrolSchedule;
use Modules\PhysicalSecurity\Filament\Resources\PatrolSchedules\Pages\EditPatrolSchedule;
use Modules\PhysicalSecurity\Filament\Resources\PatrolSchedules\Pages\ListPatrolSchedules;
use Modules\PhysicalSecurity\Filament\Resources\PatrolSchedules\Pages\ViewPatrolSchedule;
use Modules\PhysicalSecurity\Filament\Resources\PatrolSchedules\Schemas\PatrolScheduleForm;
use Modules\PhysicalSecurity\Filament\Resources\PatrolSchedules\Schemas\PatrolScheduleInfolist;
use Modules\PhysicalSecurity\Filament\Resources\PatrolSchedules\Tables\PatrolSchedulesTable;
use Modules\PhysicalSecurity\Models\PatrolSchedule;

class PatrolScheduleResource extends ModuleResource
{
    protected static ?string $model = PatrolSchedule::class;

    public static function form(Schema $schema): Schema
    {
        return PatrolScheduleForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PatrolScheduleInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PatrolSchedulesTable::configure($table);
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
            'index' => ListPatrolSchedules::route('/'),
            'create' => CreatePatrolSchedule::route('/create'),
            'view' => ViewPatrolSchedule::route('/{record}'),
            'edit' => EditPatrolSchedule::route('/{record}/edit'),
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
