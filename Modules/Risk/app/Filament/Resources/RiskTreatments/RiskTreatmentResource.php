<?php

namespace Modules\Risk\Filament\Resources\RiskTreatments;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Risk\Filament\Resources\RiskTreatments\Pages\CreateRiskTreatment;
use Modules\Risk\Filament\Resources\RiskTreatments\Pages\EditRiskTreatment;
use Modules\Risk\Filament\Resources\RiskTreatments\Pages\ListRiskTreatments;
use Modules\Risk\Filament\Resources\RiskTreatments\Pages\ViewRiskTreatment;
use Modules\Risk\Filament\Resources\RiskTreatments\Schemas\RiskTreatmentForm;
use Modules\Risk\Filament\Resources\RiskTreatments\Schemas\RiskTreatmentInfolist;
use Modules\Risk\Filament\Resources\RiskTreatments\Tables\RiskTreatmentsTable;
use Modules\Risk\Models\RiskTreatment;

class RiskTreatmentResource extends ModuleResource
{
    protected static ?string $model = RiskTreatment::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return RiskTreatmentForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return RiskTreatmentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RiskTreatmentsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRiskTreatments::route('/'),
            'create' => CreateRiskTreatment::route('/create'),
            'view' => ViewRiskTreatment::route('/{record}'),
            'edit' => EditRiskTreatment::route('/{record}/edit'),
        ];
    }
}
