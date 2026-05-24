<?php

namespace Modules\Campus\Filament\Resources\LecturerEvaluations;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Campus\Filament\Resources\LecturerEvaluations\Pages\CreateLecturerEvaluation;
use Modules\Campus\Filament\Resources\LecturerEvaluations\Pages\EditLecturerEvaluation;
use Modules\Campus\Filament\Resources\LecturerEvaluations\Pages\ListLecturerEvaluations;
use Modules\Campus\Filament\Resources\LecturerEvaluations\Pages\ViewLecturerEvaluation;
use Modules\Campus\Filament\Resources\LecturerEvaluations\Schemas\LecturerEvaluationForm;
use Modules\Campus\Filament\Resources\LecturerEvaluations\Schemas\LecturerEvaluationInfolist;
use Modules\Campus\Filament\Resources\LecturerEvaluations\Tables\LecturerEvaluationsTable;
use Modules\Campus\Models\LecturerEvaluation;
use Modules\Core\Filament\Support\ModuleResource;

class LecturerEvaluationResource extends ModuleResource
{
    protected static ?string $model = LecturerEvaluation::class;

    public static function form(Schema $schema): Schema
    {
        return LecturerEvaluationForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return LecturerEvaluationInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LecturerEvaluationsTable::configure($table);
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
            'index' => ListLecturerEvaluations::route('/'),
            'create' => CreateLecturerEvaluation::route('/create'),
            'view' => ViewLecturerEvaluation::route('/{record}'),
            'edit' => EditLecturerEvaluation::route('/{record}/edit'),
        ];
    }
}
