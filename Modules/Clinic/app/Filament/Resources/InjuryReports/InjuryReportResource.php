<?php

namespace Modules\Clinic\Filament\Resources\InjuryReports;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Clinic\Filament\Resources\InjuryReports\Pages\CreateInjuryReport;
use Modules\Clinic\Filament\Resources\InjuryReports\Pages\EditInjuryReport;
use Modules\Clinic\Filament\Resources\InjuryReports\Pages\ListInjuryReports;
use Modules\Clinic\Filament\Resources\InjuryReports\Pages\ViewInjuryReport;
use Modules\Clinic\Filament\Resources\InjuryReports\Schemas\InjuryReportForm;
use Modules\Clinic\Filament\Resources\InjuryReports\Schemas\InjuryReportInfolist;
use Modules\Clinic\Filament\Resources\InjuryReports\Tables\InjuryReportsTable;
use Modules\Clinic\Models\InjuryReport;
use Modules\Core\Filament\Support\ModuleResource;

class InjuryReportResource extends ModuleResource
{
    protected static ?string $model = InjuryReport::class;

    public static function form(Schema $schema): Schema
    {
        return InjuryReportForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return InjuryReportInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return InjuryReportsTable::configure($table);
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
            'index' => ListInjuryReports::route('/'),
            'create' => CreateInjuryReport::route('/create'),
            'view' => ViewInjuryReport::route('/{record}'),
            'edit' => EditInjuryReport::route('/{record}/edit'),
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
