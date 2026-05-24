<?php

namespace Modules\Core\Filament\Resources\ParentSurveyResponses;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Resources\ParentSurveyResponses\Pages\CreateParentSurveyResponse;
use Modules\Core\Filament\Resources\ParentSurveyResponses\Pages\EditParentSurveyResponse;
use Modules\Core\Filament\Resources\ParentSurveyResponses\Pages\ListParentSurveyResponses;
use Modules\Core\Filament\Resources\ParentSurveyResponses\Pages\ViewParentSurveyResponse;
use Modules\Core\Filament\Resources\ParentSurveyResponses\Schemas\ParentSurveyResponseForm;
use Modules\Core\Filament\Resources\ParentSurveyResponses\Schemas\ParentSurveyResponseInfolist;
use Modules\Core\Filament\Resources\ParentSurveyResponses\Tables\ParentSurveyResponsesTable;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Core\Models\ParentSurveyResponse;

class ParentSurveyResponseResource extends ModuleResource
{
    protected static ?string $model = ParentSurveyResponse::class;

    public static function form(Schema $schema): Schema
    {
        return ParentSurveyResponseForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ParentSurveyResponseInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ParentSurveyResponsesTable::configure($table);
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
            'index' => ListParentSurveyResponses::route('/'),
            'create' => CreateParentSurveyResponse::route('/create'),
            'view' => ViewParentSurveyResponse::route('/{record}'),
            'edit' => EditParentSurveyResponse::route('/{record}/edit'),
        ];
    }
}
