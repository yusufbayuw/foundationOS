<?php

namespace Modules\EducationQa\Filament\Resources\QualitySurveys;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\EducationQa\Filament\Resources\QualitySurveys\Pages\CreateQualitySurvey;
use Modules\EducationQa\Filament\Resources\QualitySurveys\Pages\EditQualitySurvey;
use Modules\EducationQa\Filament\Resources\QualitySurveys\Pages\ListQualitySurveys;
use Modules\EducationQa\Filament\Resources\QualitySurveys\Pages\ViewQualitySurvey;
use Modules\EducationQa\Filament\Resources\QualitySurveys\Schemas\QualitySurveyForm;
use Modules\EducationQa\Filament\Resources\QualitySurveys\Schemas\QualitySurveyInfolist;
use Modules\EducationQa\Filament\Resources\QualitySurveys\Tables\QualitySurveysTable;
use Modules\EducationQa\Models\QualitySurvey;

class QualitySurveyResource extends ModuleResource
{
    protected static ?string $model = QualitySurvey::class;

    public static function form(Schema $schema): Schema
    {
        return QualitySurveyForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return QualitySurveyInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return QualitySurveysTable::configure($table);
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
            'index' => ListQualitySurveys::route('/'),
            'create' => CreateQualitySurvey::route('/create'),
            'view' => ViewQualitySurvey::route('/{record}'),
            'edit' => EditQualitySurvey::route('/{record}/edit'),
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
