<?php

namespace Modules\Enrollment\Filament\Resources\AdmissionPeriods;

use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Enrollment\Filament\Resources\AdmissionPeriods\Pages\CreateAdmissionPeriod;
use Modules\Enrollment\Filament\Resources\AdmissionPeriods\Pages\EditAdmissionPeriod;
use Modules\Enrollment\Filament\Resources\AdmissionPeriods\Pages\ListAdmissionPeriods;
use Modules\Enrollment\Filament\Resources\AdmissionPeriods\Pages\ViewAdmissionPeriod;
use Modules\Enrollment\Filament\Resources\AdmissionPeriods\Schemas\AdmissionPeriodForm;
use Modules\Enrollment\Filament\Resources\AdmissionPeriods\Schemas\AdmissionPeriodInfolist;
use Modules\Enrollment\Filament\Resources\AdmissionPeriods\Tables\AdmissionPeriodsTable;
use Modules\Enrollment\Models\AdmissionPeriod;

class AdmissionPeriodResource extends LocalizedResource
{
    protected static ?string $model = AdmissionPeriod::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return AdmissionPeriodForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AdmissionPeriodInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AdmissionPeriodsTable::configure($table);
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
            'index' => ListAdmissionPeriods::route('/'),
            'create' => CreateAdmissionPeriod::route('/create'),
            'view' => ViewAdmissionPeriod::route('/{record}'),
            'edit' => EditAdmissionPeriod::route('/{record}/edit'),
        ];
    }
}
