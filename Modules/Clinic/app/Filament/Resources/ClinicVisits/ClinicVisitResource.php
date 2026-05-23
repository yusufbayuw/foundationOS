<?php

namespace Modules\Clinic\Filament\Resources\ClinicVisits;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Clinic\Filament\Resources\ClinicVisits\Pages\CreateClinicVisit;
use Modules\Clinic\Filament\Resources\ClinicVisits\Pages\EditClinicVisit;
use Modules\Clinic\Filament\Resources\ClinicVisits\Pages\ListClinicVisits;
use Modules\Clinic\Filament\Resources\ClinicVisits\Pages\ViewClinicVisit;
use Modules\Clinic\Filament\Resources\ClinicVisits\Schemas\ClinicVisitForm;
use Modules\Clinic\Filament\Resources\ClinicVisits\Tables\ClinicVisitsTable;
use Modules\Clinic\Models\ClinicVisit;
use Modules\Core\Filament\Support\ModuleResource;

class ClinicVisitResource extends ModuleResource
{
    protected static ?string $model = ClinicVisit::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return ClinicVisitForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ClinicVisitsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListClinicVisits::route('/'),
            'create' => CreateClinicVisit::route('/create'),
            'view' => ViewClinicVisit::route('/{record}'),
            'edit' => EditClinicVisit::route('/{record}/edit'),
        ];
    }
}
