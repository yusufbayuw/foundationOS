<?php

namespace Modules\EducationQa\Filament\Resources\QualityIndicators;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\EducationQa\Filament\Resources\QualityIndicators\Pages\CreateQualityIndicator;
use Modules\EducationQa\Filament\Resources\QualityIndicators\Pages\EditQualityIndicator;
use Modules\EducationQa\Filament\Resources\QualityIndicators\Pages\ListQualityIndicators;
use Modules\EducationQa\Filament\Resources\QualityIndicators\Pages\ViewQualityIndicator;
use Modules\EducationQa\Filament\Resources\QualityIndicators\Schemas\QualityIndicatorForm;
use Modules\EducationQa\Filament\Resources\QualityIndicators\Schemas\QualityIndicatorInfolist;
use Modules\EducationQa\Filament\Resources\QualityIndicators\Tables\QualityIndicatorsTable;
use Modules\EducationQa\Models\QualityIndicator;

class QualityIndicatorResource extends ModuleResource
{
    protected static ?string $model = QualityIndicator::class;

    public static function form(Schema $schema): Schema
    {
        return QualityIndicatorForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return QualityIndicatorInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return QualityIndicatorsTable::configure($table);
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
            'index' => ListQualityIndicators::route('/'),
            'create' => CreateQualityIndicator::route('/create'),
            'view' => ViewQualityIndicator::route('/{record}'),
            'edit' => EditQualityIndicator::route('/{record}/edit'),
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
