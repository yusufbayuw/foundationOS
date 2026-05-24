<?php

namespace Modules\PhysicalSecurity\Filament\Resources\PatrolLogs;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\PhysicalSecurity\Filament\Resources\PatrolLogs\Pages\CreatePatrolLog;
use Modules\PhysicalSecurity\Filament\Resources\PatrolLogs\Pages\EditPatrolLog;
use Modules\PhysicalSecurity\Filament\Resources\PatrolLogs\Pages\ListPatrolLogs;
use Modules\PhysicalSecurity\Filament\Resources\PatrolLogs\Pages\ViewPatrolLog;
use Modules\PhysicalSecurity\Filament\Resources\PatrolLogs\Schemas\PatrolLogForm;
use Modules\PhysicalSecurity\Filament\Resources\PatrolLogs\Schemas\PatrolLogInfolist;
use Modules\PhysicalSecurity\Filament\Resources\PatrolLogs\Tables\PatrolLogsTable;
use Modules\PhysicalSecurity\Models\PatrolLog;

class PatrolLogResource extends ModuleResource
{
    protected static ?string $model = PatrolLog::class;

    public static function form(Schema $schema): Schema
    {
        return PatrolLogForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PatrolLogInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PatrolLogsTable::configure($table);
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
            'index' => ListPatrolLogs::route('/'),
            'create' => CreatePatrolLog::route('/create'),
            'view' => ViewPatrolLog::route('/{record}'),
            'edit' => EditPatrolLog::route('/{record}/edit'),
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
