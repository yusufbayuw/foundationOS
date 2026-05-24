<?php

namespace Modules\PhysicalSecurity\Filament\Resources\VisitorLogs;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\PhysicalSecurity\Filament\Resources\VisitorLogs\Pages\CreateVisitorLog;
use Modules\PhysicalSecurity\Filament\Resources\VisitorLogs\Pages\EditVisitorLog;
use Modules\PhysicalSecurity\Filament\Resources\VisitorLogs\Pages\ListVisitorLogs;
use Modules\PhysicalSecurity\Filament\Resources\VisitorLogs\Pages\ViewVisitorLog;
use Modules\PhysicalSecurity\Filament\Resources\VisitorLogs\Schemas\VisitorLogForm;
use Modules\PhysicalSecurity\Filament\Resources\VisitorLogs\Schemas\VisitorLogInfolist;
use Modules\PhysicalSecurity\Filament\Resources\VisitorLogs\Tables\VisitorLogsTable;
use Modules\PhysicalSecurity\Models\VisitorLog;

class VisitorLogResource extends ModuleResource
{
    protected static ?string $model = VisitorLog::class;

    public static function form(Schema $schema): Schema
    {
        return VisitorLogForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return VisitorLogInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VisitorLogsTable::configure($table);
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
            'index' => ListVisitorLogs::route('/'),
            'create' => CreateVisitorLog::route('/create'),
            'view' => ViewVisitorLog::route('/{record}'),
            'edit' => EditVisitorLog::route('/{record}/edit'),
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
