<?php

namespace Modules\Counseling\Filament\Resources\WellbeingSurveys;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Counseling\Filament\Resources\WellbeingSurveys\Pages\CreateWellbeingSurvey;
use Modules\Counseling\Filament\Resources\WellbeingSurveys\Pages\EditWellbeingSurvey;
use Modules\Counseling\Filament\Resources\WellbeingSurveys\Pages\ListWellbeingSurveys;
use Modules\Counseling\Filament\Resources\WellbeingSurveys\Pages\ViewWellbeingSurvey;
use Modules\Counseling\Filament\Resources\WellbeingSurveys\Schemas\WellbeingSurveyForm;
use Modules\Counseling\Filament\Resources\WellbeingSurveys\Schemas\WellbeingSurveyInfolist;
use Modules\Counseling\Filament\Resources\WellbeingSurveys\Tables\WellbeingSurveysTable;
use Modules\Counseling\Models\WellbeingSurvey;

class WellbeingSurveyResource extends ModuleResource
{
    protected static ?string $model = WellbeingSurvey::class;

    public static function form(Schema $schema): Schema
    {
        return WellbeingSurveyForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return WellbeingSurveyInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WellbeingSurveysTable::configure($table);
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
            'index' => ListWellbeingSurveys::route('/'),
            'create' => CreateWellbeingSurvey::route('/create'),
            'view' => ViewWellbeingSurvey::route('/{record}'),
            'edit' => EditWellbeingSurvey::route('/{record}/edit'),
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
