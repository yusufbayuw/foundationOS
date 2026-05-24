<?php

namespace Modules\Consulting\Filament\Resources\ConsultantTimesheets;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Consulting\Filament\Resources\ConsultantTimesheets\Pages\CreateConsultantTimesheet;
use Modules\Consulting\Filament\Resources\ConsultantTimesheets\Pages\EditConsultantTimesheet;
use Modules\Consulting\Filament\Resources\ConsultantTimesheets\Pages\ListConsultantTimesheets;
use Modules\Consulting\Filament\Resources\ConsultantTimesheets\Pages\ViewConsultantTimesheet;
use Modules\Consulting\Filament\Resources\ConsultantTimesheets\Schemas\ConsultantTimesheetForm;
use Modules\Consulting\Filament\Resources\ConsultantTimesheets\Schemas\ConsultantTimesheetInfolist;
use Modules\Consulting\Filament\Resources\ConsultantTimesheets\Tables\ConsultantTimesheetsTable;
use Modules\Consulting\Models\ConsultantTimesheet;
use Modules\Core\Filament\Support\ModuleResource;

class ConsultantTimesheetResource extends ModuleResource
{
    protected static ?string $model = ConsultantTimesheet::class;

    public static function form(Schema $schema): Schema
    {
        return ConsultantTimesheetForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ConsultantTimesheetInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ConsultantTimesheetsTable::configure($table);
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
            'index' => ListConsultantTimesheets::route('/'),
            'create' => CreateConsultantTimesheet::route('/create'),
            'view' => ViewConsultantTimesheet::route('/{record}'),
            'edit' => EditConsultantTimesheet::route('/{record}/edit'),
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
