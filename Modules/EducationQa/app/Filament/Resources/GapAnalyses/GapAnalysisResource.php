<?php

namespace Modules\EducationQa\Filament\Resources\GapAnalyses;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\EducationQa\Filament\Resources\GapAnalyses\Pages\CreateGapAnalysis;
use Modules\EducationQa\Filament\Resources\GapAnalyses\Pages\EditGapAnalysis;
use Modules\EducationQa\Filament\Resources\GapAnalyses\Pages\ListGapAnalyses;
use Modules\EducationQa\Filament\Resources\GapAnalyses\Pages\ViewGapAnalysis;
use Modules\EducationQa\Filament\Resources\GapAnalyses\Schemas\GapAnalysisForm;
use Modules\EducationQa\Filament\Resources\GapAnalyses\Schemas\GapAnalysisInfolist;
use Modules\EducationQa\Filament\Resources\GapAnalyses\Tables\GapAnalysesTable;
use Modules\EducationQa\Models\GapAnalysis;

class GapAnalysisResource extends ModuleResource
{
    protected static ?string $model = GapAnalysis::class;

    public static function form(Schema $schema): Schema
    {
        return GapAnalysisForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return GapAnalysisInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GapAnalysesTable::configure($table);
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
            'index' => ListGapAnalyses::route('/'),
            'create' => CreateGapAnalysis::route('/create'),
            'view' => ViewGapAnalysis::route('/{record}'),
            'edit' => EditGapAnalysis::route('/{record}/edit'),
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
