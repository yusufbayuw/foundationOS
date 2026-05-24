<?php

namespace Modules\EducationQa\Filament\Resources\SurveyResponses;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\EducationQa\Filament\Resources\SurveyResponses\Pages\CreateSurveyResponse;
use Modules\EducationQa\Filament\Resources\SurveyResponses\Pages\EditSurveyResponse;
use Modules\EducationQa\Filament\Resources\SurveyResponses\Pages\ListSurveyResponses;
use Modules\EducationQa\Filament\Resources\SurveyResponses\Pages\ViewSurveyResponse;
use Modules\EducationQa\Filament\Resources\SurveyResponses\Schemas\SurveyResponseForm;
use Modules\EducationQa\Filament\Resources\SurveyResponses\Schemas\SurveyResponseInfolist;
use Modules\EducationQa\Filament\Resources\SurveyResponses\Tables\SurveyResponsesTable;
use Modules\EducationQa\Models\SurveyResponse;

class SurveyResponseResource extends ModuleResource
{
    protected static ?string $model = SurveyResponse::class;

    public static function form(Schema $schema): Schema
    {
        return SurveyResponseForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SurveyResponseInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SurveyResponsesTable::configure($table);
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
            'index' => ListSurveyResponses::route('/'),
            'create' => CreateSurveyResponse::route('/create'),
            'view' => ViewSurveyResponse::route('/{record}'),
            'edit' => EditSurveyResponse::route('/{record}/edit'),
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
