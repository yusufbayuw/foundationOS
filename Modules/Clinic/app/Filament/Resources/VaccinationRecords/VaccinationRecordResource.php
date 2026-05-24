<?php

namespace Modules\Clinic\Filament\Resources\VaccinationRecords;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Clinic\Filament\Resources\VaccinationRecords\Pages\CreateVaccinationRecord;
use Modules\Clinic\Filament\Resources\VaccinationRecords\Pages\EditVaccinationRecord;
use Modules\Clinic\Filament\Resources\VaccinationRecords\Pages\ListVaccinationRecords;
use Modules\Clinic\Filament\Resources\VaccinationRecords\Pages\ViewVaccinationRecord;
use Modules\Clinic\Filament\Resources\VaccinationRecords\Schemas\VaccinationRecordForm;
use Modules\Clinic\Filament\Resources\VaccinationRecords\Schemas\VaccinationRecordInfolist;
use Modules\Clinic\Filament\Resources\VaccinationRecords\Tables\VaccinationRecordsTable;
use Modules\Clinic\Models\VaccinationRecord;
use Modules\Core\Filament\Support\ModuleResource;

class VaccinationRecordResource extends ModuleResource
{
    protected static ?string $model = VaccinationRecord::class;

    public static function form(Schema $schema): Schema
    {
        return VaccinationRecordForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return VaccinationRecordInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VaccinationRecordsTable::configure($table);
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
            'index' => ListVaccinationRecords::route('/'),
            'create' => CreateVaccinationRecord::route('/create'),
            'view' => ViewVaccinationRecord::route('/{record}'),
            'edit' => EditVaccinationRecord::route('/{record}/edit'),
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
