<?php

namespace Modules\Risk\Filament\Resources\RiskAssessments;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Risk\Filament\Resources\RiskAssessments\Pages\CreateRiskAssessment;
use Modules\Risk\Filament\Resources\RiskAssessments\Pages\EditRiskAssessment;
use Modules\Risk\Filament\Resources\RiskAssessments\Pages\ListRiskAssessments;
use Modules\Risk\Filament\Resources\RiskAssessments\Pages\ViewRiskAssessment;
use Modules\Risk\Filament\Resources\RiskAssessments\Schemas\RiskAssessmentForm;
use Modules\Risk\Filament\Resources\RiskAssessments\Schemas\RiskAssessmentInfolist;
use Modules\Risk\Filament\Resources\RiskAssessments\Tables\RiskAssessmentsTable;
use Modules\Risk\Models\RiskAssessment;

class RiskAssessmentResource extends ModuleResource
{
    protected static ?string $model = RiskAssessment::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return RiskAssessmentForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return RiskAssessmentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RiskAssessmentsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRiskAssessments::route('/'),
            'create' => CreateRiskAssessment::route('/create'),
            'view' => ViewRiskAssessment::route('/{record}'),
            'edit' => EditRiskAssessment::route('/{record}/edit'),
        ];
    }
}
