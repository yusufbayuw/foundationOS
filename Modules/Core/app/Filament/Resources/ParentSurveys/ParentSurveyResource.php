<?php

namespace Modules\Core\Filament\Resources\ParentSurveys;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Resources\ParentSurveys\Pages\CreateParentSurvey;
use Modules\Core\Filament\Resources\ParentSurveys\Pages\EditParentSurvey;
use Modules\Core\Filament\Resources\ParentSurveys\Pages\ListParentSurveys;
use Modules\Core\Filament\Resources\ParentSurveys\Pages\ViewParentSurvey;
use Modules\Core\Filament\Resources\ParentSurveys\Schemas\ParentSurveyForm;
use Modules\Core\Filament\Resources\ParentSurveys\Schemas\ParentSurveyInfolist;
use Modules\Core\Filament\Resources\ParentSurveys\Tables\ParentSurveysTable;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Core\Models\ParentSurvey;

class ParentSurveyResource extends ModuleResource
{
    protected static ?string $model = ParentSurvey::class;

    public static function form(Schema $schema): Schema
    {
        return ParentSurveyForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ParentSurveyInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ParentSurveysTable::configure($table);
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
            'index' => ListParentSurveys::route('/'),
            'create' => CreateParentSurvey::route('/create'),
            'view' => ViewParentSurvey::route('/{record}'),
            'edit' => EditParentSurvey::route('/{record}/edit'),
        ];
    }
}
